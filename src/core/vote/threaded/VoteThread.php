<?php
declare(strict_types=1);

namespace core\vote\threaded;

use core\main\text\message\Message;
use core\main\text\TextFormat;
use core\network\Links;
use core\vote\cache\VotingCache;
use core\vote\events\PlayerVoteClaimEvent;
use core\vote\events\PlayerVoteEvent;
use DateTime;
use DateTimeZone;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\thread\Thread;
use Threaded;

class VoteThread extends Thread
{

    //CREDITS TO MY BOY TWISTED EVEN THOUGH HE DIDNT KNOW I TOOK THIS LEL

    /**
     * Identifiers used to identify actions between threads.
     */
    public const ACTION_VALIDATE_VOTE = 0;
    public const ACTION_CLAIM_VOTE = 1;
    public const ACTION_UPDATE_CACHE = 2;

    /**
     * Values returned from MinecraftPocket-Servers when validating a vote.
     */
    public const VOTE_STATUS_NOT_VOTED = "0";
    public const VOTE_STATUS_CLAIMABLE = "1";
    public const VOTE_STATUS_CLAIMED = "2";

    private $running;

    private $apiKey;
    private $actionQueue;
    private $actionResults;


    public function __construct()
    {
        $this->actionQueue = new Threaded();
        $this->actionResults = new Threaded();
        self::start(PTHREADS_INHERIT_NONE);
    }

    public function onRun(): void
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        while ($this->running) {
            $action_data = $this->actionQueue->shift();
            while ($action_data !== null) {
                $action = igbinary_unserialize($action_data);
                switch ($action["type"]) {
                    case self::ACTION_VALIDATE_VOTE:
                        $player = $action["player"];
                        curl_setopt($ch, CURLOPT_URL, $this->getApiUrl("object=votes&element=claim&key=" . $this->apiKey . "&username=" . str_replace(" ", "%20", $player)));
                        curl_setopt($ch, CURLOPT_POST, false);

                        $action["result"] = curl_exec($ch);

                        $this->actionResults[] = igbinary_serialize($action);

                        break;
                    case self::ACTION_CLAIM_VOTE:
                        $player = $action["player"];
                        curl_setopt($ch, CURLOPT_URL, $this->getApiUrl("action=post&object=votes&element=claim&key=" . $this->apiKey . "&username=" . str_replace(" ", "%20", $player)));
                        curl_setopt($ch, CURLOPT_POST, true);

                        $action["result"] = curl_exec($ch);

                        $this->actionResults[] = igbinary_serialize($action);

                        break;
                    case self::ACTION_UPDATE_CACHE:
                        curl_setopt($ch, CURLOPT_URL, $this->getApiUrl("object=servers&element=detail&key=" . $this->apiKey));
                        curl_setopt($ch, CURLOPT_POST, false);

                        $infoResult = curl_exec($ch);
                        $action["info"] = [
                            "uptime" => "0%",
                            "score" => "0",
                            "rank" => "0",
                            "votes" => "0",
                            "favorited" => "0",
                            "comments" => "0"
                        ];
                        if ($infoResult !== false) {
                            $info = json_decode($infoResult, true);
                            if (json_last_error() === JSON_ERROR_NONE) {
                                $action["info"] = [
                                    "uptime" => $info["uptime"] . "%",
                                    "score" => $info["score"],
                                    "rank" => $info["rank"],
                                    "votes" => $info["votes"],
                                    "favorited" => $info["favorited"],
                                    "comments" => $info["comments"]
                                ];
                            }
                        }

                        curl_setopt($ch, CURLOPT_URL, $this->getApiUrl("object=servers&element=voters&key=" . $this->apiKey . "&month=current&format=json&limit=10"));
                        curl_setopt($ch, CURLOPT_POST, false);

                        $topResult = curl_exec($ch);
                        $action["top"] = [];
                        if ($topResult !== false) {
                            $top = json_decode($topResult, true);
                            if (json_last_error() === JSON_ERROR_NONE) {
                                foreach ($top["voters"] as $voter) {
                                    $action["top"][$voter["nickname"]] = $voter["votes"];
                                }
                            }
                        }

                        curl_setopt($ch, CURLOPT_URL, $this->getApiUrl("object=servers&element=votes&key=" . $this->apiKey . "&format=json&limit=1000"));
                        curl_setopt($ch, CURLOPT_POST, false);

                        $unclaimedResult = curl_exec($ch);
                        $action["unclaimed"] = [];
                        if ($unclaimedResult !== false) {
                            $votes = json_decode($unclaimedResult, true);
                            if (json_last_error() === JSON_ERROR_NONE) {
                                foreach ($votes["votes"] as $vote) {
                                    $date = new DateTime("now", new DateTimeZone("America/New_York"));
                                    if (strpos($vote["date"], $date->format("F jS, Y")) === 0 && $vote["claimed"] === "0") {
                                        $action["unclaimed"][] = $vote["nickname"];
                                    }
                                }
                            }
                        }

                        $this->actionResults[] = igbinary_serialize($action);

                        break;
                }
            }
        }
    }

    /**
     * Appends $args to the MinecraftPocket-Servers API URL to be used for http requests.
     *
     * @param string $args
     *
     * @return string
     */
    private function getApiUrl(string $args): string
    {
        return "https://minecraftpocket-servers.com/api/?" . $args;
    }

    /**
     * Returns wether an action is already in the queue, used to limit queue spam.
     *
     * @param int $action
     * @param Player|null $player
     *
     * @return bool
     */
    public function isActionInQueue(int $action, ?Player $player = null): bool
    {
        foreach ($this->actionQueue as $queued) {
            $queued = igbinary_unserialize($queued);
            if ($queued["type"] === $action && ($player === null || $queued["player"] === $player->getName())) {
                return true;
            }
        }

        return false;
    }

    public function setApiKey(string $apiKey): void
    {
        $this->apiKey = $apiKey;
        if (!$this->running && !$this->isStarted() && $apiKey !== "") {
            $this->running = true;
            $this->start();
        } elseif ($apiKey === "") {
            if ($this->running) {
                $this->running = false;
            }
            Server::getInstance()->getLogger()->info("Invalid Vote Key");
        }
    }

    /**
     * "Collects" all of the action results from the thread and handles them if needed.
     *
     * @param Server $server
     */
    public function collectActionResults(Server $server): void
    {
        while (($result = $this->actionResults->shift()) !== null) {
            $result = igbinary_unserialize($result);
            switch ($result["type"]) {
                case self::ACTION_VALIDATE_VOTE:
                    $player = $server->getPlayerExact($result["player"]);
                    if ($player !== null && $player->isOnline()) {
                        switch ($result["result"]) {
                            case self::VOTE_STATUS_NOT_VOTED:
                                $player->sendMessage(Message::PREFIX . "You have not voted, go to " . TextFormat::LIGHT_PURPLE . Links::VOTE . TextFormat::GRAY . " to vote!");
                                break;
                            case self::VOTE_STATUS_CLAIMABLE:
                                ($event = new PlayerVoteEvent($player))->call();
                                if ($event->isCancelled()) {
                                    return;
                                }
                                $this->addActionToQueue(self::ACTION_CLAIM_VOTE, $player);

                                break;
                            case self::VOTE_STATUS_CLAIMED:
                                $player->sendMessage(Message::PREFIX . "You've already voted!");

                                break;
                            default:
                                $player->sendMessage(Message::PREFIX . "An error occurred, please try again later!");
                                break;
                        }
                    }

                    break;
                case self::ACTION_CLAIM_VOTE:
                    $player = $server->getPlayerExact($result["player"]);
                    if ($player !== null && $player->isOnline()) {
                        if ($result["result"] === "1") {
                            ($event = new PlayerVoteClaimEvent($player))->call();
                            if ($event->isCancelled()) {
                                return;
                            }
                            $player->sendMessage(Message::PREFIX . "You've successfully claimed your vote!");
                        } else {
                            $player->sendMessage(Message::PREFIX . "An error occurred, please try again later!");
                        }
                    }

                    break;
                case self::ACTION_UPDATE_CACHE:
                    VotingCache::setServerInfo($result["info"]);
                    VotingCache::setTopVoters($result["top"]);
                    VotingCache::setUnclaimedVotes($unclaimed = $result["unclaimed"]);
                    foreach ($unclaimed as $target) {
                        $target = $server->getPlayerExact($target);
                        if ($target !== null && $target->isOnline()) {
                            $this->addActionToQueue(self::ACTION_CLAIM_VOTE, $target);
                        }
                    }
                    break;
            }
        }
    }

    /**
     * Adds an action to the queue that will be executed on the next run.
     *
     * @param int $action
     * @param Player|null $player
     */
    public function addActionToQueue(int $action, ?Player $player = null): void
    {
        $toAdd = ["type" => $action];
        if ($player !== null) {
            $toAdd["player"] = $player->getName();
        }
        $this->actionQueue[] = igbinary_serialize($toAdd);

        $this->synchronized(function (): void {
            $this->notify();
        });
    }
}

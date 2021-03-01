<?php
declare(strict_types=1);

namespace core\network\objects;

class NetworkPlayer
{

    private $xuid;
    private $username;
    private $ip;
    private $ip_locked;
    private $password_locked;
    private $password;
    private $discord;
    private $youtube;
    private $description;
    private $mail;

    public function __construct(string $xuid, string $username, string $ip, bool $ip_locked, bool $password_locked, string $password, string $discord, string $youtube, string $description, array $mail = [])
    {
        $this->xuid = $xuid;
        $this->username = $username;
        $this->ip = $ip;
        $this->ip_locked = $ip_locked;
        $this->password_locked = $password_locked;
        $this->password = $password;
        $this->discord = $discord;
        $this->youtube = $youtube;
        $this->description = $description;
        $this->mail = $mail;
    }

    public function getXuid(): string
    {
        return $this->xuid;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function setIp(string $value): void
    {
        $this->ip = $value;
    }

    public function isIpLocked(): bool
    {
        return $this->ip_locked;
    }

    public function setIpLocked(bool $value): void
    {
        $this->ip_locked = $value;
    }

    public function isPasswordLocked(): bool
    {
        return $this->password_locked;
    }

    public function setPasswordLocked(bool $value): void
    {
        $this->password_locked = $value;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $value): void
    {
        $this->password = md5($value);
    }

    //----------------------------------------------

    public function getDiscord(): string
    {
        return $this->discord;
    }

    public function setDiscord(string $value): void
    {
        $this->discord = $value;
    }

    public function getYoutube(): string
    {
        return $this->youtube;
    }

    public function setYoutube(string $value): void
    {
        $this->youtube = $value;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $value): void
    {
        $this->description = $value;
    }

    public function getMail(): array
    {
        return $this->mail;
    }

    public function addMail(array $value): void
    {
        $this->mail[$value[0]] = $value;
    }

    public function removeMail(string $id): void
    {
        if (isset($this->mail[$id])) {
            unset($this->mail[$id]);
        }
    }
}
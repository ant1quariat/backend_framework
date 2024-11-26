<?php

namespace skygoose\backend_framework\network;

interface ISocialNetwork
{
    function login(): bool|array;
    function logout(): bool;
    function getToken(): array|false;
}
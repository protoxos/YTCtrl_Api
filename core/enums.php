<?php
    abstract class Status
    {
        const Error = 0;
        const Success = 1;
        const Warning = 2;
        const Unknow = 3;
    }

    abstract class ActionIds {
        const None = 0;
        const PlayVideo = 1;
        const SetVolume = 2;
        const ToogleFullscreen = 3;
    }
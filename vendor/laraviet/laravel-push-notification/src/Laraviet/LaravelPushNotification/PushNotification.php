<?php namespace Laraviet\LaravelPushNotification;

class PushNotification {

    public function app($appName)
    {
        return new App(\Config::get('packages.laraviet.laravel-push-notification.config.'.$appName));        
    }

    public function Message()
    {
      $instance = (new \ReflectionClass('Sly\NotificationPusher\Model\Message'));
        return $instance->newInstanceArgs(func_get_args());
    }

    public function Device()
    {
      $instance = (new \ReflectionClass('Sly\NotificationPusher\Model\Device'));
        return $instance->newInstanceArgs(func_get_args());
    }

    public function DeviceCollection()
    {
      $instance = (new \ReflectionClass('Sly\NotificationPusher\Collection\DeviceCollection'));
        return $instance->newInstanceArgs(func_get_args());
    }

    public function PushManager()
    {
      $instance = (new \ReflectionClass('Sly\NotificationPusher\PushManager'));
        return $instance->newInstanceArgs(func_get_args());
    }

    public function ApnsAdapter()
    {
      $instance = (new \ReflectionClass('Sly\NotificationPusher\Adapter\ApnsAdapter'));
        return $instance->newInstanceArgs(func_get_args());
    }

    public function GcmAdapter()
    {
      $instance = (new \ReflectionClass('Sly\NotificationPusher\Model\GcmAdapter'));
        return $instance->newInstanceArgs(func_get_args());
    }

    public function Push()
    {
      $instance = (new \ReflectionClass('Sly\NotificationPusher\Model\Push'));
        return $instance->newInstanceArgs(func_get_args());
    }

}
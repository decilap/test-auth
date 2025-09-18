<?php

namespace App\Enum;

// Centralized PHP 8.1+ backed enums used by entities.

enum GenderEnum: string
{
    case Male = 'male';
    case Female = 'female';
    case NonBinary = 'non_binary';
    case Other = 'other';
    case PreferNotToSay = 'prefer_not_to_say';
}

enum OrientationEnum: string
{
    case Straight = 'straight';
    case Gay = 'gay';
    case Lesbian = 'lesbian';
    case Bisexual = 'bisexual';
    case Asexual = 'asexual';
    case Pansexual = 'pansexual';
    case Queer = 'queer';
    case Other = 'other';
    case PreferNotToSay = 'prefer_not_to_say';
}

enum LikeTypeEnum: string
{
    case Like = 'like';
    case Pass = 'pass';
    case SuperLike = 'super_like';
}

enum MatchStatusEnum: string
{
    case Active = 'active';
    case Unmatched = 'unmatched';
    case Reported = 'reported';
    case Blocked = 'blocked';
}

enum MsgTypeEnum: string
{
    case Text = 'text';
    case Emoji = 'emoji';
    case Image = 'image';
    case System = 'system';
}

enum SubscriptionStatusEnum: string
{
    case Active = 'active';
    case Trialing = 'trialing';
    case PastDue = 'past_due';
    case Canceled = 'canceled';
    case Incomplete = 'incomplete';
    case IncompleteExpired = 'incomplete_expired';
}

enum PaymentStatusEnum: string
{
    case Succeeded = 'succeeded';
    case Pending = 'pending';
    case Failed = 'failed';
    case Refunded = 'refunded';
    case Disputed = 'disputed';
}

enum NotifTypeEnum: string
{
    case Message = 'message';
    case Match = 'match';
    case Like = 'like';
    case Visit = 'visit';
    case Subscription = 'subscription';
    case System = 'system';
}

enum NotifChannelEnum: string
{
    case Push = 'push';
    case InApp = 'in_app';
    case Email = 'email';
    case Sms = 'sms';
}

enum DeviceOsEnum: string
{
    case Ios = 'ios';
    case Android = 'android';
    case Web = 'web';
}

enum PrivacyEnum: string
{
    case Everyone = 'everyone';
    case Matches = 'matches';
    case Nobody = 'nobody';
}

enum VerificationEnum: string
{
    case Unverified = 'unverified';
    case EmailVerified = 'email_verified';
    case PhotoVerified = 'photo_verified';
    case IdVerified = 'id_verified';
}
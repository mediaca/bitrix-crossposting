<?php

declare(strict_types=1);

namespace Mediaca\Crossposting\Vk\Api\WallPost;

enum TypeMediaAttachment: string
{
    case PHOTO = 'photo';
    case VIDEO = 'video';
    case DOC = 'doc';
    case AUDIO = 'audio';
    case PAGE = 'page';
    case NOTE = 'note';
    case POLL = 'poll';
    case ALBUM = 'album';
    case MARKET = 'market';
    case MARKET_ALBUM = 'market_album';
    case AUDIO_PLAYLIST = 'audio_playlist';
}

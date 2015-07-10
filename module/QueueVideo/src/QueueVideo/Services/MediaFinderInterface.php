<?php

namespace QueueVideo\Services;

interface MediaFinderInterface {
    public function __construct($requester);
    public function searchForPlaylists($searchString, $limit);
    public function searchForMediaItems($searchString, $limit);
    public function getMediaItemById($mediaItemId);
    public function getMediaItemsByPlaylistId($playlistId);
    public function parseMediaItemFromResponse($response);
    public function parseMediaItemsFromResponse($response);
    public function parsePlaylistFromResponse($response);
}
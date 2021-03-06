<?php
require 'vendor/autoload.php';

use PicoFeed\Reader\Reader;

Class FeedReader {
    protected $url;

    protected $out = ['audioData' => []];

    public function __construct($url) {
        $this->url = $url;
    }

    public function getAudioData() {
        try {

            //see https://github.com/fguillot/picoFeed/blob/master/docs/feed-parsing.markdown
            $reader = new Reader;
            $resource = $reader->download($this->url);

            $parser = $reader->getParser(
                $resource->getUrl(),
                $resource->getContent(),
                $resource->getEncoding()
            );

            $feed = $parser->execute();
            foreach ($feed->items as $item) {
                $data = [];
                $data['title'] = $item->getTitle();
                $data['url'] = str_replace('http:', 'https:', $item->getEnclosureUrl());
                $this->out['audioData'][] = $data;
                // For the db cache.
                $data['date'] = date('U', $data['date']);
            }
            return json_encode($this->out);
        }
        catch (Exception $e) {
            // Do something...
        }
    }

}

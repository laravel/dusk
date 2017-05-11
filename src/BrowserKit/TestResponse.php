<?php

namespace Laravel\Dusk\BrowserKit;

use Symfony\Component\DomCrawler\Crawler;
use Laravel\Dusk\BrowserKit\Concerns\InteractsWithPages;
use \Illuminate\Foundation\Testing\TestResponse as FoundationTestResponse;

class TestResponse extends FoundationTestResponse
{
    use InteractsWithPages;

    /**
     * The current URL being viewed.
     *
     * @var string
     */
    protected $currentUri;

    /**
     * The DomCrawler instance.
     *
     * @var \Symfony\Component\DomCrawler\Crawler
     */
    protected $crawler;

    /**
     * Nested crawler instances used by the "within" method.
     *
     * @var array
     */
    protected $subCrawlers = [];

    /**
     * All of the stored inputs for the current page.
     *
     * @var array
     */
    protected $inputs = [];

    /**
     * All of the stored uploads for the current page.
     *
     * @var array
     */
    protected $uploads = [];

    /**
     * Set the current URI and initialize the crawler
     *
     * @param  string  $currentUri
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function initialize($currentUri)
    {
        $this->crawler = new Crawler($this->getContent(), $currentUri);

        $this->currentUri = $currentUri;

        return $this;
    }
}

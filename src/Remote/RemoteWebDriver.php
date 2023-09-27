<?php

namespace Laravel\Dusk\Remote;

use Facebook\WebDriver\Exception\Internal\UnexpectedResponseException;
use Facebook\WebDriver\Remote\DriverCommand;
use Facebook\WebDriver\Remote\JsonWireCompat;

class RemoteWebDriver extends \Facebook\WebDriver\Remote\RemoteWebDriver
{
    /**
     * Fetch all WebDriverElements within the current page using the given mechanism.
     *
     * @return \Generator|RemoteWebElement[] A list of all WebDriverElements, or an empty array if nothing matches
     *
     * @see WebDriverBy
     */
    public function fetchElements(WebDriverBy $by)
    {
        $raw_elements = $this->execute(
            DriverCommand::FIND_ELEMENTS,
            JsonWireCompat::getUsing($by, $this->isW3cCompliant)
        );

        if (! is_array($raw_elements)) {
            throw UnexpectedResponseException::forError('Server response to findElements command is not an array');
        }

        foreach ($raw_elements as $raw_element) {
            yield $this->newElement(JsonWireCompat::getElement($raw_element));
        }
    }
}

<?php

namespace Laminas\Feed\Reader\Extension\CreativeCommons;

use Laminas\Feed\Reader\Exception\RuntimeException;
use Laminas\Feed\Reader\Extension;

use function array_key_exists;
use function array_unique;
use function get_class;
use function gettype;
use function is_object;
use function is_string;
use function sprintf;

class Feed extends Extension\AbstractFeed
{
    /**
     * Get the entry license
     *
     * @param  int $index
     * @return null|string
     */
    public function getLicense($index = 0)
    {
        $licenses = $this->getLicenses();

        if (! isset($licenses[$index])) {
            return null;
        }

        if (! is_string($licenses[$index])) {
            throw new RuntimeException(sprintf(
                'Unable to retrieve license; expected string, received "%s"',
                is_object($licenses[$index]) ? get_class($licenses[$index]) : gettype($licenses[$index])
            ));
        }

        return $licenses[$index];
    }

    /**
     * Get the entry licenses
     *
     * @return array
     */
    public function getLicenses()
    {
        $name = 'licenses';
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        $licenses = [];
        $list     = $this->xpath->evaluate('channel/cc:license');

        if ($list->length) {
            foreach ($list as $license) {
                $licenses[] = $license->nodeValue;
            }

            $licenses = array_unique($licenses);
        }

        $this->data[$name] = $licenses;

        return $this->data[$name];
    }

    /**
     * Register Creative Commons namespaces
     *
     * @return void
     */
    protected function registerNamespaces()
    {
        $this->xpath->registerNamespace('cc', 'http://backend.userland.com/creativeCommonsRssModule');
    }
}

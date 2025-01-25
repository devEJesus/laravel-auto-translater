<?php

namespace LaravelAutoTranslator\Core;

use DOMDocument;
use DOMNode;

class Html
{
    public static function fromArray(array $data): string|false
    {
        $dom = new DOMDocument('1.0', 'UTF-8');

        // Create the HTML structure
        $html = $dom->createElement('html');
        $body = $dom->createElement('body');

        foreach ($data as $key => $value) {
            // Always create the div element first
            $divElement = $dom->createElement('div');
            $divElement->setAttribute('class', $key);

            if (is_array($value)) {
                // Handle nested arrays
                $innerHtml = self::fromArray($value);
                if ($innerHtml) {
                    // Parse the inner HTML into a DOMDocument
                    $innerDom = new DOMDocument;
                    $innerDom->loadHTML($innerHtml, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

                    if ($innerDom->documentElement == null) {
                        throw new \Exception('DocumentElement is null');
                    }
                    // Import and append each child node to the current DOM
                    foreach ($innerDom->documentElement->childNodes as $childNode) {
                        $importedNode = $dom->importNode($childNode, true);
                        $divElement->appendChild($importedNode);
                    }
                }

            } else {
                // Split the text by <param> tags
                $pattern = '/(<param>.*?<\/param>)/';
                $parts = preg_split($pattern, $value, -1, PREG_SPLIT_DELIM_CAPTURE);
                if ($parts == false) {
                    throw new \Exception('Error split string translation');
                }

                foreach ($parts as $part) {
                    if (preg_match('/^<param>(.*?)<\/param>$/', $part, $matches)) {
                        // Create element with translate="no"
                        $spanElement = $dom->createElement('span', $matches[1]);
                        $spanElement->setAttribute('translate', 'no');
                        $divElement->appendChild($spanElement);
                    } else {
                        // Regular text
                        if (! empty($part)) {
                            $textNode = $dom->createTextNode($part);
                            $divElement->appendChild($textNode);
                        }
                    }
                }
            }

            $body->appendChild($divElement);
        }

        $html->appendChild($body);
        $dom->appendChild($html);

        // Save HTML and ensure it's correctly formatted
        $dom->formatOutput = true;

        return $dom->saveHTML($body); // Only return the inner body content
    }

    public static function toArray(string $html): array
    {
        $dom = new DOMDocument('1.0', 'UTF-8');

        // Convert HTML entities to UTF-8 characters before loading
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

        // Suppress warnings and load with UTF-8 encoding
        @$dom->loadHTML(
            '<?xml encoding="UTF-8">'.$html,
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );

        $body = $dom->getElementsByTagName('body')->item(0);
        if (! $body) {
            return [];
        }

        return self::parseDivElements($body);
    }

    private static function parseDivElements(DOMNode $node): array
    {
        $result = [];

        foreach ($node->childNodes as $child) {
            if ($child->nodeName === 'div') {
                // @phpstan-ignore-next-line
                $key = $child->getAttribute('class');
                if (empty($key)) {
                    continue;
                }

                // Check if the div has nested divs
                $nestedDivs = false;
                foreach ($child->childNodes as $subChild) {
                    if ($subChild->nodeName === 'div') {
                        $nestedDivs = true;
                        break;
                    }
                }

                if ($nestedDivs) {
                    // Recursively parse nested divs
                    $result[$key] = self::parseDivElements($child);
                } else {
                    // Extract text and spans
                    $value = '';
                    foreach ($child->childNodes as $subChild) {
                        if ($subChild->nodeType === XML_TEXT_NODE && $subChild->nodeValue) {
                            $value .= trim($subChild->nodeValue);
                            // @phpstan-ignore-next-line
                        } elseif ($subChild->nodeName === 'span' && $subChild->getAttribute('translate') === 'no' && $subChild->nodeValue) {
                            $value .= '<param>'.trim($subChild->nodeValue).'</param>';
                        }
                    }
                    $result[$key] = $value;
                }
            }
        }

        return $result;
    }
}

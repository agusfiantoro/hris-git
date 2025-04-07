<?php
namespace App\Http\Requests\Support;

use DOMDocument;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

/**
 * A class to sanitize user input request during validation.
 * 
 * PHP version ^7.4, ^8 (Requires support for type hinting, including class props type hint)
 * 
 * @category Class
 * @package App\Http\Requests\Support;
 * @author Faisal Hanif <faisal.hanif@borwita.co.id>
 * @license MIT
 */
class SanitizedForm extends FormRequest
{
    /**
     * Prepare and sanitize the data for validation.
     * If you were to override this method, make sure to invoke sanitize() method.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->sanitize();
    }

    /**
     * Sanitizes data that are validated with 'string' rule.
     *
     * @return void
     */
    public function sanitize(): void
    {
        $rules = $this->rules();
        foreach($rules as $input => $rule) {
            $rule = is_array($rule) ? $rule : explode("|", $rule);
            if(in_array('string', $rule)) {
                // If rules contains string validation
                // Split key in case of array input condition
                $input = explode(".", $input);
                if(count($input) < 2) {
                    $input = $input[0];
                    if(key_exists($input, $this->all()) && $this->{$input}) {
                        // And if rule key exists on the request and is not empty, replace with tag-stripped value
                        $this->merge([
                            $input => strip_tags($this->{$input}),
                        ]);
                    }
                } else if($this->{$input[0]}) {
                    // If rules exist in array input
                    $parameters = $this->all();
                    array_walk($parameters[$input[0]], function (&$object) use($input) {
                        if(key_exists($input[2], $object)) {
                            $object[$input[2]] = strip_tags($object[$input[2]]);
                        }
                    });
                    $this->merge($parameters);
                }
            }
        }
    }

    /**
     * Sanitizes data that are validated with 'string' rule. 
     * For standalone use with validation without extending FormRequest class. 
     * 
     * @param Request   $request Request object
     * @param array     $rules Validation rules
     * @return Request
     */
    public static function sanitizeStringInput(Request $request, array $rules)
    {
        foreach($rules as $input => $rule) {
            $rule = is_array($rule) ? $rule : explode("|", $rule);
            if(in_array('string', $rule)) {
                // If rules contains string validation
                // Split key in case of array input condition
                $input = explode(".", $input);
                if(count($input) < 2) {
                    $input = $input[0];
                    if(key_exists($input, $request->all()) && $request->{$input}) {
                        // And if rule key exists on the request and is not empty, replace with tag-stripped value
                        $request->merge([
                            $input => strip_tags($request->{$input}),
                        ]);
                    }
                } else if($request->{$input[0]}) {
                    // If rules exist in array input
                    $parameters = $request->all();
                    array_walk($parameters[$input[0]], function (&$object) use($input) {
                        if(key_exists($input[2], $object)) {
                            $object[$input[2]] = strip_tags($object[$input[2]]);
                        }
                    });
                    $request->merge($parameters);
                }
            }
        }
        return $request;
    }

    /**
     * Accepts HTML string as input and strips its attributes, leaving only "class", "id", and "style".
     * If script tags exists and not included in allowed tags, it will also be removed. 
     * For standalone use with validation without extending FormRequest class. **It is not advised to 
     * modify the allowed tags and allowed attributes** unless it is necessary.
     * @param string    $htmlString     The HTML string
     * @param array     $allowedTags    An array containing HTML tags allowlist
     * @param array     $allowedAttrs   An array containing HTML tag attributes allowlist
     *
     * @return string|false|null
     */
    public static function stripUnsafeTagsAndAttrs(
        string $htmlString, 
        array $allowedTags = ["html", "body", "b", "br", "div", "nav", "em", "hr", "i", "li", "ol", "p", "s", "section", "span", "table", "tr", "td", "u", "ul"],
        array $allowedAttrs = ["class", "id", "style"]
    ) 
    {
        $xml = new DOMDocument();
        libxml_use_internal_errors(true);

        if(!strlen($htmlString)) {
            return null;
        }
        if($xml->loadHTML($htmlString, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD)) {
            foreach ($xml->getElementsByTagName("*") as $tag) {
                if(!in_array($tag->tagName, $allowedTags)) {
                    $tag->parentNode->removeChild($tag);
                } else {
                    foreach ($tag->attributes as $attr) {
                        if (!in_array($attr->nodeName, $allowedAttrs)){
                            $tag->removeAttribute($attr->nodeName);
                        }
                    }
                }
            }
        }
        return $xml->saveHTML();
    }
}
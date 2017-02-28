<?php
namespace Insightful\Parser;

use GuzzleHttp\Client;

/**
 * Created by PhpStorm.
 * User: joe
 * Date: 27/02/2017
 * Time: 17:33
 */
class ReviewParser
{
    const CSAPI_URL = "https://westus.api.cognitive.microsoft.com/text/analytics/v2.0/";
    const API_ENV_KEY = "MS_API_KEY";

    private static function buildDocument(string $reviewDocument, string $id) : array
    {
        $document = [];                    //create document
        $document['language'] = 'en';
        $document['id'] = (string)$id;
        $document['text'] = $reviewDocument;

        return $document;
    }

    private static function getHeaders() : array
    {
        //header necessary for every post request
        return [
            'Ocp-Apim-Subscription-Key' => env(self::API_ENV_KEY),
            'Content-Type'              => 'application/json',
            'Accept'                    => 'application/json'
        ];
    }

    // Splits a sentence smartly
    // http://stackoverflow.com/questions/16377437/split-a-text-into-sentences
    private static function splitToSentence(string $string) : array
    {
        return preg_split('/(?<=[.?!])\s+(?=[a-z])/i', $string);
    }


    //Takes a review and splits it into documents with incrementing IDs
    private static function splitIntoDocuments(string $text, int $index) : array
    {
        $sentenceOffset = 0;

        // Index documents by the Id of the review and a decimal, eg 1.0,1.1...1.10
        $sentences = self::splitToSentence($text);        //split by sentence
        $documents = [];
        foreach ($sentences as $sentence) {
            $id = (string)$index . "." . $sentenceOffset++;
            $documents[] = self::buildDocument($sentence, $id);
        }

        return $documents;
    }


    /**
     *
     * @param array $reviews Array of \Insight\Review objects
     * @return array Returns Array of arrays of sentiment and phrases
     *                       Returns Array of arrays of sentiment and phrases
     * @internal param array $review_texts String array of review text
     */
    public static function parseReviews(array $reviews) : array
    {

        $numReviews = sizeof($reviews);
        //Don't for-see this happening soon. Eventually below could will need to be split to batch based on 10k Document limit
        if ($numReviews > 3000) {
            echo "Fatal Error: More than 3000 (" . $numReviews . ")reviews being parsed into one call. Entries may be lost due to API limits";
            die();
        }


        $client = new Client(['base_uri' => self::CSAPI_URL]);


        $documents = [];
        foreach ($reviews as $review) {            //each full review
            $documents[] = self::splitIntoDocuments($review->text, $review->id);
        }

        //Collection of Documents to be sent
        $body = [                                            //body of API call
            "documents" => $documents
        ];

        try{
            $phrase_response = $client->post('keyPhrases/', [    //key phrases API call
                'headers' => self::getHeaders(),
                'json'    => $body
            ]);

            $sentiment_response = $client->post('sentiment/', [    //sentiment API call
                'headers' => self::getHeaders(),
                'json'    => $body
            ]);

            $sentiment = json_decode($sentiment_response->getBody(), true);//decode response to array that can be parsed
            $phrases = json_decode($phrase_response->getBody(), true);

            $sentiments = $sentiment['documents'];
            $keyPhrases = $phrases['documents'];


            return ['sentiment' => $sentiments, 'phrases' => $keyPhrases];

        } catch (\GuzzleHttp\Exception\ClientException $e){
            //Catch error - print it and throw again for command line to catch
            $failMessage = $e->getResponse()->getBody()->getContents();
            $decoded = json_decode($failMessage, true);
            var_dump($decoded);
            throw $e;
        }
    }
}
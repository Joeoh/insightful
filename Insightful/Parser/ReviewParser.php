<?php
namespace Insightful\Parser;

use GuzzleHttp\Client;
use Insightful\Review;
use Insightful\Sentence;

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

    private static function buildDocument(string $documentText, string $id) : array
    {
        $document = [];                    //create document
        $document['language'] = 'en';
        $document['id'] = (string)$id;
        $document['text'] = $documentText;

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



    /**
     *
     * @param \Illuminate\Database\Eloquent\Collection $sentences of \Insight\Sentence objects
     * @return array Returns Array of arrays of sentiment and phrases
     *                       Returns Array of arrays of sentiment and phrases
     * @internal param array $review_texts String array of review text
     */
    public static function parseSentences($sentences) : array
    {

        //Nothing to do
        if (sizeof($sentences) == 0) {
            return ['sentiment' => [], 'phrases' => []];
        }

        $client = new Client(['base_uri' => self::CSAPI_URL]);


        $documents = [];
        foreach ($sentences as $sentence) {            //each sentence
            $documents[] = self::buildDocument($sentence->text, $sentence->id);
        }

        $parts = array_chunk($documents, 1000);
        $sentiments = array();
        $keyPhrases = array();
        //Break the documents up into parts less than 1000
        foreach ($parts as $part) {


            //Collection of Documents to be sent
            $body = [                                            //body of API call
                "documents" => $part
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


                $sentiment = json_decode($sentiment_response->getBody(),
                    true);//decode response to array that can be parsed
                $phrases = json_decode($phrase_response->getBody(), true);

                $sentiments = array_merge($sentiments, $sentiment['documents']);
                $keyPhrases = array_merge($keyPhrases ,$phrases['documents']);



            } catch (\GuzzleHttp\Exception\ClientException $e){
                //Catch error - print it and throw again for command line to catch
                print_r($e->getResponse()->getBody());
                $failMessage = $e->getResponse()->getBody()->getContents();
                $decoded = json_decode($failMessage, true);
                print_r($decoded);
                throw $e;
            }
        }
        return ['sentiment' => $sentiments, 'phrases' => $keyPhrases];

    }


    //Split reviews into sentences and store
    public static function parseReviewsToSentences()
    {
        $reviews = Review::all()->where('parsed',false);


        foreach ($reviews as $review){
            $sentences = self::splitToSentence($review->text);
            $i = 0;
            foreach ($sentences as $sentenceText){
                $sentence = new Sentence;
                $sentence->text = $sentenceText;
                $sentence->review_id = $review->id;
                $sentence->position = $i++;
                $sentence->parsed = false;
                $sentence->save();
            }

            $review->parsed = true;
            $review->save();
        }

        return $reviews;
    }
}
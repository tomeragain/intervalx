<?php


namespace App\Responses;


class RentalResponse
{
    static function Ok($line = 0) {
        return ['response' => 'interval created', 'success' => true, 'line' => $line];
    }

    static function OkDelete($line = 0) {
        return ['response' => 'interval correctly deleted', 'success' => true, 'line' => $line];
    }

    static function OkFlush($line = 0) {
        return ['response' => 'data truncated', 'success' => true, 'line' => $line];
    }

    static function basicFailure($line = 0) {
        return ['response' => 'an error has occurred', 'errors' => true, 'line' => $line];
    }

    static function invalidInterval($line = 0) {
        return ['response' => 'The interval submitted is invalid', 'errors' => true, 'line' => $line];
    }

    static function alreadyExists() {
        return ['response' => 'Interval already exists'];
    }

}
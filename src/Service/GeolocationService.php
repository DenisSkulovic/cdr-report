<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeolocationService
{
    private const COUNTRY_INFO_FILE = __DIR__ . '/../../var/data/countryInfo.txt';

    private HttpClientInterface $httpClient;
    private string $apiKey;
    private array $prefixToContinent = [];

    public function __construct(HttpClientInterface $httpClient, string $geolocationApiKey)
    {
        $this->httpClient = $httpClient;
        $this->apiKey = $geolocationApiKey;
        $this->loadCountryPrefixes();
    }

    public function getContinentByIp(string $ip): string
    {
        $url = "https://api.ipgeolocation.io/ipgeo?apiKey={$this->apiKey}&ip=" . urlencode($ip);

        try {
            $response = $this->httpClient->request('GET', $url);
            $data = $response->toArray();
            return $data['continent_code'] ?? 'UN';
        } catch (\Throwable $e) {
            return 'UN'; // fallback continent
        }
    }

    public function getContinentByPhone(string $phone): string
    {
        // Try longest prefixes first (e.g. 972, 971 before 97)
        for ($len = 4; $len >= 1; $len--) {
            $prefix = substr($phone, 0, $len);
            if (isset($this->prefixToContinent[$prefix])) {
                return $this->prefixToContinent[$prefix];
            }
        }

        return 'UN';
    }

    private function loadCountryPrefixes(): void
    {
        if (!file_exists(self::COUNTRY_INFO_FILE)) {
            throw new \RuntimeException("GeoNames countryInfo.txt not found at " . self::COUNTRY_INFO_FILE);
        }

        $lines = file(self::COUNTRY_INFO_FILE);
        foreach ($lines as $line) {
            if (str_starts_with($line, '#')) continue;

            $parts = explode("\t", $line);
            if (count($parts) > 9) {
                $countryCode = $parts[0];         // ISO code
                $continent = $parts[8];           // Continent code
                $phonePrefix = $parts[12] ?? '';  // Phone prefix

                if (!empty($phonePrefix)) {
                    foreach (explode(',', $phonePrefix) as $prefix) {
                        $this->prefixToContinent[trim($prefix)] = $continent;
                    }
                }
            }
        }
    }
}
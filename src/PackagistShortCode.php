<?php

namespace WebbuildersGroup\PackagistShortcode;

use Psr\SimpleCache\CacheInterface;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Model\ModelData;
use SilverStripe\View\Requirements;
use SilverStripe\View\SSViewer;
use SilverStripe\View\ViewableData;

class PackagistShortCode
{
    use Configurable;
    public static function parse($arguments, $content = null, $parser = null)
    {

        if (!array_key_exists('package', $arguments) || empty($arguments['package']) || strpos($arguments['package'], '/') <= 0) {
            return '<p><i>Packagist package undefined</i></p>';
        }

        //Get Config
        $config = self::config();


        $obj = (class_exists(ModelData::class)) ? new ModelData() : new ViewableData();

        //Add the Respository Setting
        $obj->Package = $arguments['package'];

        //Add the button config
        if (array_key_exists('mode', $arguments) && ($arguments['mode'] == 'total' || $arguments['mode'] == 'monthly' || $arguments['mode'] == 'daily')) {
            $obj->DisplayMode = $arguments['mode'];
        } else {
            $obj->DisplayMode = 'total';
        }

        //Retrieve Stats
        $cacheKey = md5('packagistshortcode_' . $arguments['package']);
        $cache = Injector::inst()->get(CacheInterface::class . '.PackagistShortCode');

        if (!$cache->has($cacheKey)) {
            $response = self::getFromAPI($arguments['package']);

            //Verify a 200, if not say the repo errored out and cache false
            if (empty($response) || $response === false || !property_exists($response, 'package')) {
                $cachedData = array('total' => 'N/A', 'monthly' => 'N/A', 'daily' => 'N/A');
            } else {
                if ($config->UseShortHandNumbers == true) {
                    $totalDownloads = self::shortHandNumber($response->package->downloads->total);
                    $monthlyDownloads = self::shortHandNumber($response->package->downloads->monthly);
                    $dailyDownloads = self::shortHandNumber($response->package->downloads->daily);
                } else {
                    $totalDownloads = number_format($response->package->downloads->total);
                    $monthlyDownloads = number_format($response->package->downloads->monthly);
                    $dailyDownloads = number_format($response->package->downloads->daily);
                }

                $cachedData = array('total' => $totalDownloads, 'monthly' => $monthlyDownloads, 'daily' => $dailyDownloads);
            }

            //Cache response to file system
            $cache->set($cacheKey, serialize($cachedData));
        } else {
            $cachedData = unserialize($cache->get($cacheKey));
        }


        $obj->TotalDownloads = $cachedData['total'];
        $obj->MonthlyDownloads = $cachedData['monthly'];
        $obj->DailyDownloads = $cachedData['daily'];


        //Init ss viewer and render
        Requirements::css('webbuilders-group/silverstripe-packagistshortcode:css/PackagistButton.css');

        $ssViewer = new SSViewer('Includes/PackagistButton');
        return $ssViewer->process($obj);
    }

    /**
     * Loads the data from the github api
     * @param {string} $url URL to load
     * @return {stdObject} Returns the JSON Response from the GitHub API
     *
     * @see http://developer.github.com/v3/repos/#get
     */
    final protected static function getFromAPI($repo)
    {
        if (function_exists('curl_init') && $ch = curl_init()) {
            curl_setopt($ch, CURLOPT_URL, 'https://packagist.org/packages/' . $repo . '.json');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);


            $contents = json_decode(curl_exec($ch));
            curl_close($ch);

            return $contents;
        } else {
            user_error('CURL is not available', E_USER_ERROR);
        }
    }

    /**
     * Gets the short hand of the given number so 1000 becomes 1k, 2000 becomes 2k, and 1000000 becomes 1m etc
     * @param {int} $number Number to convert
     * @return {string} Short hand of the given number
     */
    protected static function shortHandNumber($number)
    {
        if ($number >= 1000000000) {
            return round($number / 1000000000, 1) . 'B';
        } else if ($number >= 1000000) {
            return round($number / 1000000, 1) . 'M';
        } else if ($number >= 1000) {
            return round($number / 1000, 1) . 'K';
        }

        return $number;
    }
}

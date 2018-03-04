<?php

namespace Pagekit\Blog\Controller;

use Pagekit\Application as App;

use Google_Client;

use Google_Service_Analytics;
use Google_Service_AnalyticsReporting;
use Google_Service_AnalyticsReporting_DateRange;
use Google_Service_AnalyticsReporting_Metric;
use Google_Service_AnalyticsReporting_GetReportsRequest;
use Google_Service_AnalyticsReporting_ReportRequest;
use Google_Service_AnalyticsReporting_SegmentDimensionFilter;


/**
 * @Access("blog: manage own posts || blog: manage all posts")
 * @Route("gapi", name="gapi")
 */
class AnalyticsApiController
{

    const CACHE_TIME = 3600;
    const REALTIME_CACHE_TIME = 20;

    protected $analytics;

    /**
     * @Route("/auth", methods="GET")
     */
    public function authAction()
    {
      // Creates and returns the Analytics Reporting service object.
      try{
          $config = App::module('blog')->config();
          // Create and configure a new client object.
          $client = new Google_Client();
          $client->setApplicationName("Analytics-Counters");
          $client->setAuthConfig($config['gapi']['credentials']);
          $client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/api/blog/gapi/auth');
          $client->addScope(Google_Service_Analytics::ANALYTICS_READONLY);
          $this->analytics = new Google_Service_Analytics($client);

          // Handle authorization flow from the server.
          if (! isset($_GET['code'])) {
            $auth_url = $client->createAuthUrl();
            header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
          } else {
            $client->authenticate($_GET['code']);
            App::config('blog')->set('gapi_auth_token', $client->getAccessToken());

            $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/blog/post?page=';

            return App::response()->redirect($redirect_uri);
          }
          return App::response()->json(['success' => true]);
      } catch (\Exception $e) {
          return App::response()->json(array('message' => $e->getMessage()), 400);
      }
    }


  //   /**
  //    * @Route("/api", methods="GET")
  //    * @Request({"webpage":"string", "startDate":"string", "dimensions":"string", "filters":"string", "metrics": "string", "invalidCache": "boolean", "sort":"string", "maxResults": "int"})
  //    */
  //   public function apiAction($webpage=false, $startDate=false, $dimensions='', $filters='ga:pagepath', $metrics='ga:pageviews', $invalidCache = false, $sort = false, $maxResults = false)
  //   {
  //     // getting credentials
  //     $config = App::module('blog')->config();
  //     $access_token = $config['gapi_auth_token'];
  //
  //     $client = new Google_Client();
  //     $client->addScope(Google_Service_Analytics::ANALYTICS_READONLY);
  //     $client->setAuthConfig($config['gapi']['credentials']);
  //
  //     // If the user has already authorized this app then get an access token
  //     // else redirect to ask the user to authorize access to Google Analytics.
  //     if (isset($access_token) && $access_token) {
  //         // Set the access token on the client.
  //         $client->setAccessToken($access_token);
  //         // Create an authorized analytics service object.
  //         $analytics = new Google_Service_AnalyticsReporting($client);
  //
  //         $data = array('metrics' => $metrics,
  //             'dimensions' => $dimensions,
  //             'start-date' => $config['gapi']['start_date'],
  //             'ids' => 'ga:' . $config['gapi']['credentials']['profile_id'],
  //             'end-date' => 'today',
  //             'webpage' => $webpage,
  //             'output' => 'dataTable');
  //
  //         if ($sort) {
  //             $data['sort'] = $sort;
  //         }
  //
  //         if ($maxResults) {
  //             $data['max-results'] = $maxResults;
  //         }
  //
  //         if ($filters) {
  //             $data['filters'] = $filters;
  //         }
  //
  //         try {
  //
  //             $report = $this->getReport($analytics, $data);
  //             $result['visits'] = $this->getViews($report);
  //             $result['time'] = time();
  //
  //             return App::response()->json($result);
  //
  //         } catch (\Exception $e) {
  //             return App::response()->json(array('message' => $e->getMessage()), 400);
  //         }
  //
  //     } else {
  //        $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/api/blog/gapi/auth';
  //        return App::response()->redirect($redirect_uri);
  //     }
  // }
  //
  //   protected function getReport($analytics, $search) {
  //     //valid operators can be found here: https://developers.google.com/analytics/devguides/reporting/core/v4/rest/v4/reports/batchGet#FilterLogicalOperator
  //     $query = [
  //         "viewId" => $search['ids'],
  //         "dateRanges" => [
  //             "startDate" => $search['start-date'],
  //             "endDate" => 'today'
  //         ],
  //         "metrics" => [
  //             "expression" => $search['metrics']
  //         ],
  //         "dimensionFilterClauses" => [
  //             'filters' => [
  //                 "dimension_name" => $search['filters'],
  //                 "expressions" => $search['webpage']
  //             ]
  //         ]
  //     ];
  //
  //     // build the request and response
  //     $body = new Google_Service_AnalyticsReporting_GetReportsRequest();
  //     $body->setReportRequests(array($query));
  //     //TODO:  App::get('cache')->save($url, $result, self::CACHE_TIME);
  //     $report = $analytics->reports->batchGet($body);
  //     return $report;
  //   }
  //
  //   protected function getViews($reports)
  //   {
  //       $rows = $reports[0]->getData()->getRows();
  //       if ($rows){
  //           $metrics = $rows[0]->getMetrics()[0]->values[0];
  //           if ($metrics){
  //               return $metrics;
  //           }
  //       }
  //       return 0;
  //   }
  //

    protected function disconnectAction()
    {
        unset(App::config('blog')['gapi_auth_token']);
        return App::response()->json(array());
    }
}

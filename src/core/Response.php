<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace core;

/**
 * Description of Response
 *
 * @author matthewe
 */
class Response {
  const OK_200 = 100;
  const CREATED_201= 201;
  const ACCEPTED_202 = 202;
  const AUTHORITATIVE_INFORMATION_203 = 203;
  const NO_CONTENT_204 = 204;
  const RESET_CONTENT_205 = 205;
  const PARTIAL_CONTENT_206 = 206;
  const MULTI_STATUS_207 = 207;          // RFC4918
  const ALREADY_REPORTED_208 = 208;      // RFC5842
  const IM_USED_226= 226;               // RFC3229
  //Status 3xx:
  const MULTIPLE_CHOICES_300 = 300;
  const MOVED_PERMANENTLY_301 = 301;
  const FOUND_302 = 302;
  const SEE_OTHER_303 = 303;
  const NOT_MODIFIED_304 = 304;
  const USE_PROXY_305 = 305;
  const RESERVED_306 = 306;
  const TEMPORARY_REDIRECT_307 = 307;
  const PERMANENTLY_REDIRECT_308 = 308;  // RFC7238
  //Status 4xx:
  const BAD_REQUEST_400 = 400;
  const UNAUTHORIZED_401 = 401;
  const FORBIDDEN_403 = 403;
  const NOT_FOUND_404 = 404;
  const METHOD_NOT_ALLOWED_405 = 405;
  const NOT_ACCEPTABLE_406 = 406;
  const PROXY_AUTHENTICATION_REQUIRED_407 = 407;
  const REQUEST_TIMEOUT_408 = 408;
  const CONFLICT_409 = 409;
  const GONE_410 = 410;
  const LENGTH_REQUIRED_411 = 411;
  const PRECONDITION_FAILED_412 = 412;
  const REQUEST_ENTITY_TOO_LARGE_413 = 413;
  const REQUEST_URI_TOO_LONG_414 = 414;
  const UNSUPPORTED_MEDIA_TYPE_415 = 415;
  const REQUESTED_RANGE_NOT_SATISFIABLE_416 = 416;
  const EXPECTATION_FAILED_417 = 417;                                              // RFC2324
  const MISDIRECTED_REQUEST_421 = 421;                                         // RFC7540
  const UNPROCESSABLE_ENTITY_422 = 422;                                        // RFC4918
  const LOCKED_423 = 423;                                                      // RFC4918
  const FAILED_DEPENDENCY_424 = 424;                                           // RFC4918
  const RESERVED_FOR_WEBDAV_ADVANCED_COLLECTIONS_EXPIRED_PROPOSAL_425 = 425;   // RFC2817
  const UPGRADE_REQUIRED_426 = 426;                                            // RFC2817
  const PRECONDITION_REQUIRED_428 = 428;                                       // RFC6585
  const TOO_MANY_REQUESTS_429 = 429;                                           // RFC6585
  const REQUEST_HEADER_FIELDS_TOO_LARGE_431 = 431;                             // RFC6585
  const UNAVAILABLE_FOR_LEGAL_REASONS_451 = 451;
  //Status 5xx:
  const INTERNAL_SERVER_ERROR_500 = 500;
  const NOT_IMPLEMENTED_501 = 501;
  const BAD_GATEWAY_502 = 502;
  const SERVICE_UNAVAILABLE_503 = 503;
  const GATEWAY_TIMEOUT_504 = 504;
  const VERSION_NOT_SUPPORTED_505 = 505;
  const VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL_506 = 506;                        // RFC2295
  const INSUFFICIENT_STORAGE_507 = 507;                                        // RFC4918
  const LOOP_DETECTED_508 = 508;                                               // RFC5842
  const NOT_EXTENDED_510 = 510;                                                // RFC2774
  const NETWORK_AUTHENTICATION_REQUIRED_511 = 511;                             // RFC6585

  private $host;
  
  
  /**
   * responsible for issuing error pages (404, 500 etc.)
   * Will attempt to load corresponding template in application 
   * Set corresponding headers.
   * @param type $httpCode
   */
  public static function issue($httpCode) {
    $filepath = CoreApp::rootDir() . "/html/$httpCode.php";
    if (file_exists($filepath)) {
      self::redirect($filepath);
    }
    else if (isset(self::$statusTexts[$httpCode])) {
      header($_SERVER["SERVER_PROTOCOL"] . " " . self::$statusTexts[$httpCode]);
    } else {
      header($_SERVER["SERVER_PROTOCOL"] . " " . self::$statusTexts[404]);
    }
  }
  
  public static function redirect($url, $redirectStatus = self::FOUND_302) {
    header("Location: $url", TRUE, $redirectStatus);
    exit;
  }
  
  public static function renderHTML($html, $statusCode  = self::OK_200) {
    echo $html;
  }
  
  public static $statusTexts = array(
    100 => 'Continue',
    101 => 'Switching Protocols',
    102 => 'Processing',           
    200 => 'OK',
    201 => 'Created',
    202 => 'Accepted',
    203 => 'Non-Authoritative Information',
    204 => 'No Content',
    205 => 'Reset Content',
    206 => 'Partial Content',
    207 => 'Multi-Status',        
    208 => 'Already Reported',      
    226 => 'IM Used',              
    300 => 'Multiple Choices',
    301 => 'Moved Permanently',
    302 => 'Found',
    303 => 'See Other',
    304 => 'Not Modified',
    305 => 'Use Proxy',
    307 => 'Temporary Redirect',
    308 => 'Permanent Redirect',    
    400 => 'Bad Request',
    401 => 'Unauthorized',
    402 => 'Payment Required',
    403 => 'Forbidden',
    404 => 'Not Found',
    405 => 'Method Not Allowed',
    406 => 'Not Acceptable',
    407 => 'Proxy Authentication Required',
    408 => 'Request Timeout',
    409 => 'Conflict',
    410 => 'Gone',
    411 => 'Length Required',
    412 => 'Precondition Failed',
    413 => 'Payload Too Large',
    414 => 'URI Too Long',
    415 => 'Unsupported Media Type',
    416 => 'Range Not Satisfiable',
    417 => 'Expectation Failed',
    418 => 'I\'m a teapot',                                        
    421 => 'Misdirected Request',                                        
    422 => 'Unprocessable Entity',                                       
    423 => 'Locked',                                                      
    424 => 'Failed Dependency',                                          
    425 => 'Reserved for WebDAV advanced collections expired proposal',   
    426 => 'Upgrade Required',                                            
    428 => 'Precondition Required',                                       
    429 => 'Too Many Requests',                                          
    431 => 'Request Header Fields Too Large',                             
    451 => 'Unavailable For Legal Reasons',                               
    500 => 'Internal Server Error',
    501 => 'Not Implemented',
    502 => 'Bad Gateway',
    503 => 'Service Unavailable',
    504 => 'Gateway Timeout',
    505 => 'HTTP Version Not Supported',
    506 => 'Variant Also Negotiates (Experimental)',                   
    507 => 'Insufficient Storage',                              
    508 => 'Loop Detected',                                          
    510 => 'Not Extended',                                        
    511 => 'Network Authentication Required',                           
);
}

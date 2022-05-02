<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;

class RoutingHelperService {
public function getBackUrl(Request $request, UrlMatcherInterface $matcher)
{
$referer = $request->headers->get('referer');
if (null === $referer) {
return false;
}

$url = parse_url($referer);
if ($url['host'] != $request->getHost()) {
return false;
}

// Remove the baseurl if we're in a subdir or there's no Rewrite
$path = str_replace($request->getBaseUrl(), '', $url['path']);
try {
$route = $matcher->match($path);
} catch (ResourceNotFoundException $e) {
// Doesn't match any known local route
return false;
}

// A route has been determined, check if it matches the requirement
if ('first_method' == $route['_route']) {
return $referer;
}

return false;
}
}
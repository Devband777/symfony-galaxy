<?php
namespace App\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class PlainTextResponseListener
{
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $request = $event->getRequest();

        // Check if the request header Accept contains text/plain
        if ($request->headers->get('Accept') === 'text/plain') {
            // Modify the response to return a plain text file
            $response = new Response($event->getResponse()->getContent(), 200, [
                'Content-Type' => 'text/plain',
                'Content-Disposition' => 'attachment; filename="file.txt"',
            ]);

            $event->setResponse($response);
        }
    }
}
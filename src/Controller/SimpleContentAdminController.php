<?php

namespace Hgabka\SimpleContentBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class SimpleContentAdminController extends CRUDController
{
    public function downloadCssAction($id)
    {
        $object = $this->admin->getSubject();
        if (!$object) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        $this->admin->checkAccess('edit', $object);

        $content = $object->getCss();
        $response = new Response();
        $response->setContent($content);
        $filename = 'simple-content-'.$object->getId().'.css';

        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE,
            $filename
        );

        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Transfer-Encoding', 'binary');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'must-revalidate, post-check=0, pre-check=0');
        $response->headers->set('Content-type', 'text/css');
        $response->headers->set('Content-length', \strlen($content));

        return $response;
    }
}

<?php
/*
 * (c) Suhinin Ilja <iljasuhinin@gmail.com>
 */
namespace SIP\MultipleUploadBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use SIP\MultipleUploadBundle\Upload\UploadHandler;

class UploadController extends Controller
{
    /**
     * @param $sub_dir
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \RuntimeException
     */
    public function uploadAction($sub_dir)
    {
        $request = $this->getRequest();

        $uploadDir = $this->container->getParameter('sip_multiple_upload.upload_dir');
        $uploadFolder = $this->container->getParameter('sip_multiple_upload.upload_folder');
        if ($sub_dir) {
            $uploadDir = $this->container->getParameter('sip_multiple_upload.upload_dir') . '/' . $sub_dir;
            $uploadFolder = $this->container->getParameter('sip_multiple_upload.upload_folder') . '/' . $sub_dir;

            if (!is_dir($uploadDir) && false === @mkdir($uploadDir, 0777, true)) {
                throw new \RuntimeException(sprintf('Could not create upload directory "%s".', $uploadDir));
            }

            $thumbnailsDir = $uploadDir . '/thumbnails';
            if (!is_dir($thumbnailsDir) && false === @mkdir($thumbnailsDir, 0777, true)) {
                throw new \RuntimeException(sprintf('Could not create upload directory "%s".', $thumbnailsDir));
            }
        }

        $upload_handler = new UploadHandler($uploadDir, $uploadFolder, $this->getRequest()->getBaseUrl() . $this->getRequest()->getPathInfo());

        switch ($request->getMethod()) {
            case 'OPTIONS':
                break;
            case 'HEAD':
            case 'GET':
                $upload_handler->get();
                break;
            case 'POST':
                if ($request->get('_method') === 'DELETE') {
                    $upload_handler->delete();
                } else {
                    $upload_handler->post();
                }
                break;
            case 'DELETE':
                $upload_handler->delete();
                break;
            default:
                header('HTTP/1.1 405 Method Not Allowed');
        }

        $response = new Response();
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate');
        $response->headers->set('Content-Disposition', 'inline; filename="files.json"');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', 'OPTIONS, HEAD, GET, POST, PUT, DELETE');
        $response->headers->set('Access-Control-Allow-Headers', 'X-File-Name, X-File-Type, X-File-Size');
        return $response;
    }
}

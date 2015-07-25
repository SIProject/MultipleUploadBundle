<?php
/*
 * (c) Suhinin Ilja <iljasuhinin@gmail.com>
 */
namespace SIP\MultipleUploadBundle\EventListener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\HttpFoundation\File\File;

use Genemu\Bundle\FormBundle\Gd\File\Image;
use Genemu\Bundle\FormBundle\Form\Core\EventListener\FileListener as BaseFileListener;

class FileListener extends BaseFileListener
{
    /**
     * @param FormEvent $event
     */
    public function onBind(FormEvent $event)
    {
        $data = $event->getData();

        if (empty($data)) {
            return;
        }

        if ($this->multiple) {
            $data = is_array($data)? $data[0]: $data;
            $paths = explode(',', $data);
            $return = array();

            foreach ($paths as $path) {
                if ($handle = $this->getHandleToPath($path)) {
                    $return[] = $handle;
                }
            }
        } else {
            if ($handle = $this->getHandleToPath($data)) {
                $return = $handle;
            }
        }

        $event->setData($return);
    }

    /**
     * Get Handle to Path
     *
     * @param string $path
     *
     * @return File
     */
    private function getHandleToPath($path)
    {
        $path = $this->rootDir . '/' . $this->stripQueryString($path);

        if (is_file($path)) {
            $handle = new File($path);

            if (preg_match('/image/', $handle->getMimeType())) {
                $handle = new Image($handle->getPathname());
            }

            return $handle;
        }

        return null;
    }

    /**
     * Delete info after `?`
     *
     * @param string $file
     *
     * @return string
     */
    private function stripQueryString($file)
    {
        if (false !== ($pos = strpos($file, '?'))) {
            $file = substr($file, 0, $pos);
        }

        return $file;
    }
}
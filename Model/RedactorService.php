<?php

namespace Stp\RedactorBundle\Model;

use Symfony\Component\Validator\Constraint,
    Symfony\Component\HttpFoundation\File\UploadedFile,
    Symfony\Component\Validator\Constraints\Image,
    Symfony\Component\Validator\Constraints\File,
    Symfony\Component\Validator\Exception\ValidatorException,
    Symfony\Component\DependencyInjection\ContainerAware;

class RedactorService extends ContainerAware
{

    /**
     * Options allowed for upload constraint
     *
     * @var array
     */
    protected $fileOptions = array(
        'file'  => array('maxSize', 'mimeTypes'),
        'image' => array('maxSize', 'minWidth', 'maxWidth', 'maxHeight', 'minHeight'),
    );

    /**
     * @param string $type
     * @param string $envName
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $upFile
     *
     * @return array
     */
    public function uploadFile($type, $envName, UploadedFile $upFile)
    {
        $config = $this->getConfiguration($envName);
        $config = $config['upload_' . $type];
        $uploadPath = $config['dir'];

        $originalFileName = $upFile->getClientOriginalName();
        $newFileName = sha1(uniqid()) . "." . $upFile->guessExtension();
        $uploadUrl = $config['web_dir'];

        if ($config['folders']) {
            $date = date('Y-m-d');
            $uploadPath .= DIRECTORY_SEPARATOR . $date;
            $uploadUrl .= '/' . $date;
        }

        $data = array(
	        'filename' => $originalFileName,
	        'filelink' => $uploadUrl . '/' . $newFileName
        );

        $newFile = $upFile->move($uploadPath, $newFileName);

        file_put_contents(
            $newFile->getRealPath() . ".json",
            json_encode($data), 
            LOCK_EX
        );

        return $data;
    }

    /**
     * @param string $type image/file
     * @param string $envName
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     *
     * @throws \InvalidArgumentException
     * @throws \Symfony\Component\Validator\Exception\ValidatorException
     */
    public function validateFile($type, $envName, UploadedFile $file)
    {
        if (!isset($this->fileOptions[$type])) {
            throw new \InvalidArgumentException(sprintf('Not allowed type of upload "%s"', $type));
        }
        $config = $this->getConfiguration($envName);
        $config = $config['upload_' . $type];
        $constrainConfig = array();
        //calculate options
        $allowedOptions = $this->fileOptions[$type];
        $usedOptions = array_intersect_key($config, array_flip($allowedOptions));
        $constrainConfig = array_merge($constrainConfig, $usedOptions);
        $className = sprintf('Symfony\Component\Validator\Constraints\%s', ucfirst($type));
        $constraint = new $className($constrainConfig);

        $errors = $this->container->get('validator')->validateValue($file, $constraint);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                throw new ValidatorException($error->getMessage());
            }
        }
    }

    /**
     * @param string $envName
     *
     * @return array
     */
    public function getConfiguration($envName)
    {
        return $this->container->getParameter(sprintf('stp_redactor.%s', $envName));
    }

    /**
     * @param string $envName
     *
     * @return bool
     */
    public function isAllowed($envName)
    {
        $config = $this->getConfiguration($envName);
        foreach ($config['role'] as $role) {
            if ($this->container->get('security.context')->isGranted($role) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $envName
     *
     * @return array
     */
    public function getWebConfiguration($envName)
    {
        $settings = array();
        $config = $this->container->getParameter(sprintf('stp_redactor.%s', $envName));
        $fileTypes = array('file', 'image');
        foreach($fileTypes as $type) {
            if (isset($config['upload_' . $type]) && $this->isAllowed($envName)) {
                $settings[$type . 'Upload'] = $this->container->get('router')->generate('redactor_' . $type . '_upload', array('env' => $envName));
            }
        }
        $settings = array_merge($config['settings'], $settings);

        return $settings;
    }

}

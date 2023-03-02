<?php

namespace App\Controller;

use App\Entity\MediaObject;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class upload media
 */
#[AsController]
final class CreateMediaObjectAction extends AbstractController
{
	public function __invoke(Request $request): MediaObject
	{
		$uploadedFile = $request->files->get('file');
		if (!$uploadedFile)
		{
			throw new BadRequestHttpException('"file" is required');
		}
		if(!$this->checkingFile($uploadedFile))
		{
			throw new Exception('the format or the size aren\'t supported');
		}

		$mediaObject = new MediaObject();
		$mediaObject->file = $uploadedFile;

		return $mediaObject;
	}

	// Check file type and size
	private function checkingFile($uploadedFile): ?bool
	{
		$file = $uploadedFile;
		$extension = $file->guessExtension();
		$size = $file->getSize();
		$error = $file->getError();

		$extensions = ['jpg','png', 'jpeg', 'gif', 'svg', 'webp'];
		$maxSize = 4900000;

		if(in_array($extension, $extensions))
		{
			if($size <= $maxSize)
			{
				if(!$error === 0)
				{
					throw new Exception('An error occurred during the upload');
				}
			}
			else
			{
				throw new Exception("La taille du fichier est trop volumineux ; ne doit pas depasser 90Mo");
			}

			return true;
		}
		else{
			throw new Exception("Le format n'est pas supporté");
		}
	}
}
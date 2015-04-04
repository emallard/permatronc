<?php

namespace SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\File\File;

use SiteBundle\DocUtils;
use SiteBundle\ZipUtils;

class PublicController extends Controller
{
    
    
    private $_rootPath;// = "../../permatroncfiles";///home/etienne/permaculture/dev/test/fichiers";
    
    
    private function getRootPath()
    {
        if (!isset($this->_rootPath))
        {
            $_rootPath = $this->container->getParameter('directory');
        }
        
        return $_rootPath;
    }
    
    /**
     * @Route("/debug")
     */
    public function debugAction() {
        $site = $this->container->getParameter('directory');
        
        $response = new Response(
                ' root: '.$this->getRootPath().
                ' post_max_size: '.ini_get('post_max_size').
                ' upload_max_filesize: '.ini_get('upload_max_filesize'));
        return $response;
    }
        
    /**
     * @Route("/api/ajouter-dossier")
     * @Method({"POST"})
     */
    public function ajouterDossierAction(Request $request) {
               
        $nom = $request->request->get('nom');
        $description = $request->request->get('description');
        $tags = $request->request->get('tags');
        
        if (!file_exists($this->getRootPath()."/".$nom))
        {
            mkdir($this->getRootPath()."/".$nom);

            DocUtils::setDescription($this->getRootPath()."/".$nom, $description);
            DocUtils::setTags($this->getRootPath()."/".$nom, $tags);
        }
        
        $response = new Response(DocUtils::slugify($nom));
        return $response;
    }
    
    /**
     * @Route("/api/modifier-dossier")
     * @Method({"POST"})
     */
    public function modifierDossierAction(Request $request) {
        
        //$urlSource = $request->request->get('urlSource');
        $nom = $request->request->get('nom');
        $description = $request->request->get('description');
        $tags = $request->request->get('tags');
        

        DocUtils::setDescription($this->getRootPath()."/".$nom, $description);
        DocUtils::setTags($this->getRootPath()."/".$nom, $tags);

        
        $response = new Response(DocUtils::slugify($nom));
        return $response;
    }
    
    /**
     * @Route("/api/ajouter-fichier")
     * @Method({"POST"})
     */
    public function ajouterFichierAction(Request $request) {
        
        $nom = $request->request->get('nom');
        
        foreach($request->files as $uploadedFile) {
            $name = $uploadedFile->getClientOriginalName() ;
            $file = $uploadedFile->move($this->getRootPath().'/'.$nom, $name);
        }
        
        if (file_exists($this->getRootPath().'/'.$nom.'/'.$name))
        {
            $response = new Response($this->getRootPath().'/'.$nom.'/'.$name);
        }
        else
        {
            $response = new Response("ERROR");
        }

        //$response = new Response(ini_get('post_max_size'));
        return $response;
        //$this->redirect($request->request->get('location'));
    }
    
    
    
    
     /**
     * @Route("/zip/{dossier}")
     */
    
    public function zipAction($dossier) {
        
        $dirToZip = $this->getRootPath().'/'.$dossier;
        $filename = tempnam("tmp", "zip");
        \SiteBundle\ZipUtils::createZipFolder($dirToZip, $filename);
        
        
        // Generate response
        //$response = new Response();
        $response = new Response(file_get_contents($filename));
        
        
        
        // Set headers
        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type', mime_content_type($filename));
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $dossier . '.zip";');
        $response->headers->set('Content-length', filesize($filename));

        /*
        // Send headers before outputting anything
        $response->sendHeaders();

        $response->setContent(file_get_contents($filename));
        */
        
        unlink($filename);
        return $response;
    }
    
    /**
     * @Route("/")
     * @Template("SiteBundle:Default:index.html.twig")
     */
    
    public function indexAction(Request $request) {
        
        $q = $request->query->get('q');
        if (isset($q))
        {
            $dossiers = \SiteBundle\DocUtils::getSearchDocShort($this->getRootPath(), $q);
            return array(
                "dossiers" => $dossiers);
        }
        else
        {
            $dossiers = \SiteBundle\DocUtils::getAllDocShort($this->getRootPath());
            return array(
                "dossiers" => $dossiers);
        }
    }
    
    
     /**
     * @Route("/{dossier}")
     * @Template("SiteBundle:Default:dossier.html.twig")
     */
    
    public function dossierAction($dossier) {
        
        $rootDir = $this->getRootPath();
        $dirFromUrl = DocUtils::getDirFromUrl($rootDir, $dossier);
        if (!isset($dirFromUrl))
        {
            return new Response("Ce dossier n'existe pas");
        }
        
        $contenuDossier = \SiteBundle\DocUtils::getFullDossier($this->getRootPath(), $dossier);      
        return $this->render('SiteBundle:Default:dossier.html.twig', array("dossier" => $contenuDossier));
    }
    
    /**
     * @Route("/{dossier}/{fichier}")
     */
    public function fichierAction($dossier, $fichier) {
        
        $filename = \SiteBundle\DocUtils::getFilename($this->getRootPath(), $dossier, $fichier);
        
        $file = new File($filename);
        
        $response = new Response(file_get_contents($filename));
        
        $response->headers->set('Content-Type', $file->getMimeType());
        
        return $response;
    }

    

}

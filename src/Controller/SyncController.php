<?php


namespace App\Controller;

use App\ebay\AllegroManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SyncController
 * @package App\Controller
 * @Route ("/sync")
 */
class SyncController extends MainController
{
    /**
     * @Route ("/allegro", name="syncAllegroProducts")
     * @param AllegroManager $allegroManager
     * @return RedirectResponse
     */
    public function syncAllegroProducts(AllegroManager $allegroManager): RedirectResponse
    {
        $profile = $this->session->get('currentProfile');
        if($profile) {
            $allegroManager->syncAllegroProducts($profile);

        }
        return $this->redirectToRoute('userprofile');
    }
}
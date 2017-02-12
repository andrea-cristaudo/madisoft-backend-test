<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Recipe;
use AppBundle\Form\Model\RecipeCollector;
use AppBundle\Form\Type\RecipeCollectionType;
use AppBundle\Form\Type\RecipeCollectorType;
use AppBundle\Form\Type\RecipeType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class RecipeController extends Controller
{
    /**
     * @Route("/", name="recipe_list")
     */
    public function listAction()
    {
        $recipes = $this->getDoctrine()->getRepository('AppBundle:Recipe')->findAll();

        return $this->render('recipe/list.html.twig', [
            'recipes' => $recipes,
        ]);
    }

    /**
     * @Route("/bulk-edit", name="recipe_bulk_edit")
     */
    public function bulkEditAction(Request $request)
    {
        $recipeCollector = new RecipeCollector();
        $recipeCollector->setRecipes($this->getDoctrine()->getRepository('AppBundle:Recipe')->findAll());
        $form = $this->createForm(RecipeCollectorType::class, $recipeCollector);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ('cancel' === $request->request->get('action')) {
                $this->addFlash('info', 'Recipes bulk edit aborted!');

                return $this->redirectToRoute('recipe_list');
            }

            $em = $this->getDoctrine()->getManager();
            try {
                foreach ($recipeCollector->getRecipes() as $recipe) {
                    $em->persist($recipe);
                }
                $em->flush();

                $this->addFlash('success', 'Recipes saved!');

                return $this->redirectToRoute('recipe_list');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Can\'t save recipes. Try again.');
            }
        }

        return $this->render('recipe/bulkEdit.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route("/new", name="recipe_new")
     */
    public function newAction(Request $request)
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($recipe);
                $em->flush();

                $this->addFlash('success', 'Recipe saved!');

                return $this->redirectToRoute('recipe_list');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Can\'t save the recipe. Try again.');
            }
        }

        return $this->render('recipe/new.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="recipe_edit")
     */
    public function editAction(Request $request, Recipe $recipe)
    {
        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($recipe);
                $em->flush();

                $this->addFlash('success', 'Recipe edited!');

                return $this->redirectToRoute('recipe_list');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Can\'t save the recipe. Try again.');
            }
        }

        return $this->render('recipe/edit.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route("/remove/{id}", name="recipe_remove")
     */
    public function removeAction(Request $request, Recipe $recipe)
    {
        if ($request->isMethod('post')) {
            if ('no' === $request->request->get('action')) {
                $this->addFlash('info', 'Recipe remove aborted!');

                return $this->redirectToRoute('recipe_list');
            }

            try {
                $em = $this->getDoctrine()->getManager();
                $em->remove($recipe);
                $em->flush();

                $this->addFlash('success', 'Recipe removed!');

                return $this->redirectToRoute('recipe_list');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Can\'t remove the recipe. Try again.');
            }
        }

        return $this->render('recipe/remove.html.twig', [
            'recipe' => $recipe,
        ]);
    }
}

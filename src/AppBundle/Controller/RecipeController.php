<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Ingredient;
use AppBundle\Entity\Recipe;
use AppBundle\Form\Model\RecipeCollector;
use AppBundle\Form\Type\RecipeCollectionType;
use AppBundle\Form\Type\RecipeCollectorType;
use AppBundle\Form\Type\RecipeType;
use Doctrine\Common\Collections\ArrayCollection;
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
        $recipes = $this->getDoctrine()->getRepository('AppBundle:Recipe')->findAll();

        $recipeCollector = new RecipeCollector();
        foreach ($recipes as $recipe) {
            $recipeCollector->addRecipe($recipe);
        }

        $originalRecipes = new ArrayCollection();
        foreach ($recipeCollector->getRecipes() as $recipe) {
            $originalRecipes->add($recipe);
        }

        $form = $this->createForm(RecipeCollectorType::class, $recipeCollector);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ('cancel' === $request->request->get('action')) {
                $this->addFlash('info', 'Recipes bulk edit aborted!');

                return $this->redirectToRoute('recipe_list');
            }

            $em = $this->getDoctrine()->getManager();
            try {
                foreach ($originalRecipes as $recipe) {
                    if (false === $recipeCollector->getRecipes()->contains($recipe)) {
                        $em->remove($recipe);
                    }
                }

                /** @var Recipe $recipe */
                foreach ($recipeCollector->getRecipes() as $recipe) {
                    /** @var Ingredient $ingredient */
                    foreach ($recipe->getIngredients() as $ingredient) {
                        $dbIngredient = $em->getRepository('AppBundle:Ingredient')->findOneBy(['name' => $ingredient->getName()]);
                        if (null !== $dbIngredient && $dbIngredient->getId() !== $ingredient->getId()) {
                            $dbIngredient->setDescription($ingredient->getDescription());

                            $recipe->removeIngredient($ingredient);
                            /** @var Recipe $dbRecipe */
                            foreach ($ingredient->getRecipes() as $dbRecipe) {
                                $dbRecipe->removeIngredient($ingredient);
                                $dbRecipe->addIngredient($dbIngredient);
                            }
                            $recipe->addIngredient($dbIngredient);

                            if ($ingredient->getId()) {
                                $em->remove($ingredient);
                            }
                        }
                    }

                    $em->persist($recipe);
                }
                $em->flush();

                $this->addFlash('success', 'Recipes saved!');

                return $this->redirectToRoute('recipe_list');
            } catch (\Exception $e) {
                $this->addFlash('warning', $e->getMessage());
                $this->addFlash('warning', 'Can\'t save recipes. Try again.');
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
            if ('cancel' === $request->request->get('action')) {
                $this->addFlash('info', 'Recipe creation aborted!');

                return $this->redirectToRoute('recipe_list');
            }

            $em = $this->getDoctrine()->getManager();
            try {
                /** @var Ingredient $ingredient */
                foreach ($recipe->getIngredients() as $ingredient) {
                    $dbIngredient = $em->getRepository('AppBundle:Ingredient')->findOneBy(['name' => $ingredient->getName()]);
                    if (null !== $dbIngredient && $dbIngredient->getId() !== $ingredient->getId()) {
                        $dbIngredient->setDescription($ingredient->getDescription());

                        $recipe->removeIngredient($ingredient);
                        /** @var Recipe $dbRecipe */
                        foreach ($ingredient->getRecipes() as $dbRecipe) {
                            $dbRecipe->removeIngredient($ingredient);
                            $dbRecipe->addIngredient($dbIngredient);
                            $em->persist($dbRecipe);
                        }
                        $recipe->addIngredient($dbIngredient);

                        if ($ingredient->getId()) {
                            $em->remove($ingredient);
                        }
                    }
                }

                $em->persist($recipe);
                $em->flush();

                $this->addFlash('success', 'Recipe saved!');

                return $this->redirectToRoute('recipe_list');
            } catch (\Exception $e) {
                $this->addFlash('warning', $e->getMessage());
                $this->addFlash('warning', 'Can\'t save the recipe. Try again.');
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
            if ('cancel' === $request->request->get('action')) {
                $this->addFlash('info', 'Recipe edit aborted!');

                return $this->redirectToRoute('recipe_list');
            }

            $em = $this->getDoctrine()->getManager();
            try {
                /** @var Ingredient $ingredient */
                foreach ($recipe->getIngredients() as $ingredient) {
                    $dbIngredient = $em->getRepository('AppBundle:Ingredient')->findOneBy(['name' => $ingredient->getName()]);
                    if (null !== $dbIngredient && $dbIngredient->getId() !== $ingredient->getId()) {
                        $dbIngredient->setDescription($ingredient->getDescription());

                        $recipe->removeIngredient($ingredient);
                        /** @var Recipe $dbRecipe */
                        foreach ($ingredient->getRecipes() as $dbRecipe) {
                            $dbRecipe->removeIngredient($ingredient);
                            $dbRecipe->addIngredient($dbIngredient);
                        }
                        $recipe->addIngredient($dbIngredient);

                        if ($ingredient->getId()) {
                            $em->remove($ingredient);
                        }
                    }
                }

                $em->persist($recipe);
                $em->flush();

                $this->addFlash('success', 'Recipe edited!');

                return $this->redirectToRoute('recipe_list');
            } catch (\Exception $e) {
                $this->addFlash('warning', 'Can\'t save the recipe. Try again.');
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
                $this->addFlash('warning', 'Can\'t remove the recipe. Try again.');
            }
        }

        return $this->render('recipe/remove.html.twig', [
            'recipe' => $recipe,
        ]);
    }
}

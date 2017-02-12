<?php

namespace AppBundle\Form\Model;

use AppBundle\Entity\Recipe;
use Doctrine\Common\Collections\ArrayCollection;

class RecipeCollector
{
    /** @var ArrayCollection<Recipe> */
    private $recipes;

    public function __construct()
    {
        $this->recipes = new ArrayCollection();
    }

    /**
     * @param ArrayCollection<Recipe> $recipes
     */
    public function setRecipes($recipes)
    {
        $this->recipes = $recipes;
    }

    /**
     * @return ArrayCollection<Recipe>
     */
    public function getRecipes()
    {
        return $this->recipes;
    }

    /**
     * @param Recipe $recipe
     */
    public function removeRecipe(Recipe $recipe)
    {
        $this->recipes->removeElement($recipe);
    }
}
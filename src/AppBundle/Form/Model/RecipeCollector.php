<?php

namespace AppBundle\Form\Model;

use AppBundle\Entity\Recipe;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

class RecipeCollector
{
    /**
     * @var ArrayCollection<Recipe>
     *
     * @Assert\Valid
     */
    private $recipes;

    public function __construct(ArrayCollection $recipes = null)
    {
        if (null === $recipes) {
            $recipes = new ArrayCollection();
        }

        $this->recipes = $recipes;
    }

    /**
     * @param Recipe $recipe
     *
     * @return $this
     */
    public function addRecipe(Recipe $recipe)
    {
        if (!$this->recipes->contains($recipe)) {
            $this->recipes[] = $recipe;
        }

        return $this;
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
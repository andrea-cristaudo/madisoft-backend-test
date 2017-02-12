$(function () {
    var addAddIngredientButton = function ($ingredientsDiv) {
        $ingredientsDiv.data('index', $('.ingredient', $ingredientsDiv).length);
        var $ingredientsContainerDiv = $('.ingredients', $ingredientsDiv);

        var addRemoveIngredientButton = function ($ingredientFormContainerDiv) {
            $('.ingredient', $ingredientFormContainerDiv).append(
                $('<a>')
                    .attr('href', '#')
                    .addClass('btn')
                    .addClass('btn-danger')
                    .addClass('ingredient-form-remove')
                    .text('Remove ingredient')
                    .on('click', function (e) {
                        e.preventDefault();

                        $ingredientFormContainerDiv.remove();
                    })
            );
        };

        $('.ingredient-form', $ingredientsContainerDiv).each(function () {
            addRemoveIngredientButton($(this));
        });

        var $addIngredientA = $('<a>')
            .attr('href', '#')
            .addClass('btn')
            .addClass('btn-primary')
            .text('Add ingredient')
            .appendTo(
                $('<div>')
                    .addClass('add-ingredient')
                    .appendTo($ingredientsDiv)
            )
        ;

        $addIngredientA.on('click', function (e) {
            e.preventDefault();

            var prototype = $ingredientsDiv.data('prototype');
            var index = $ingredientsDiv.data('index');
            $ingredientsDiv.data('index', index + 1);

            var $ingredientFormContainerDiv = $('<div>')
                    .addClass('col-md-4')
                    .addClass('ingredient-form')
                    .appendTo($ingredientsContainerDiv)
                    .append($(prototype.replace(/__name__label__/g, 'Ingredient n.' + (index + 1)).replace(/__name__/g, index)))
                ;

            addRemoveIngredientButton($ingredientFormContainerDiv);
        });
    };

    var $ingredientsDiv = $('.recipe-ingredients');
    $ingredientsDiv.each(function () {
        addAddIngredientButton($(this));
    });

    var $recipesContainerDiv = $('.recipes');
    if (0 !== $recipesContainerDiv.length) {
        $recipesContainerDiv.data('index', $('.recipe-form', $recipesContainerDiv).length);

        $('.recipe-form', $recipesContainerDiv).each(function () {
            var $recipeFormDiv = $(this);

            $('.add-ingredient', $recipeFormDiv).append(
                $('<a>')
                    .attr('href', '#')
                    .addClass('btn')
                    .addClass('btn-danger')
                    .text('Remove recipe')
                    .on('click', function ($recipeFormDiv) {
                        return function (e) {
                            e.preventDefault();

                            $recipeFormDiv.remove();
                        };
                    }($recipeFormDiv))
            );
        });

        $('.add-recipe a').on('click', function (e) {
            e.preventDefault();

            var prototype = $recipesContainerDiv.parent().data('prototype');
            var index = $recipesContainerDiv.data('index');
            $recipesContainerDiv.data('index', index + 1);

            var $recipeFormContainerDiv = $('<div>')
                .addClass('recipe-form')
                .appendTo($recipesContainerDiv)
                .append(
                    $(
                        prototype
                            .replace(/__name__label__/g, '')
                            .replace(/recipes___name__/g, 'recipes_' + index)
                            .replace(/\[recipes\]\[__name__\]/g, '[recipes][' + index + ']')
                    )
                )
            ;

            addAddIngredientButton($('.recipe-ingredients', $recipeFormContainerDiv));
        });
    }
});
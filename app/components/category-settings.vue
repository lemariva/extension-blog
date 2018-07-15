<template>

    <div class="uk-grid pk-grid-large pk-width-sidebar-large uk-form-stacked" data-uk-grid-margin>
        <div class="pk-width-content">
            <div class="uk-form-row">
                <input class="uk-width-1-1 uk-form-large" type="text" name="title"
                       :placeholder="'Category Title' | trans" v-model="category.title" v-validate:required>
                <p class="uk-form-help-block uk-text-danger" v-show="form.title.invalid">
                    {{ 'Title cannot be blank.' | trans }}</p>
            </div>
            <div class="uk-form-row">
                <input class="uk-width-1-1 uk-form-large" type="text" name="slug" :placeholder="'Category Slug' | trans"
                       v-model="category.slug" v-validate:required>
                <p class="uk-form-help-block uk-text-danger" v-show="form.slug.invalid">
                    {{ 'Slug cannot be blank.' | trans }}</p>
            </div>
            <div class="uk-form-row">
                <label for="form-meta-description" class="uk-form-label">{{ 'Description' | trans }}</label>
                <div class="uk-form-controls">
                    <textarea name="meta" class="uk-form-width-large" rows="5" type="text" v-model="post.meta"></textarea>
                </div>
            </div>
            <div class="uk-form-row">
                <input class="uk-width-1-1 uk-form-large" type="text" name="color" :placeholder="'Category Color' | trans"
                       v-model="category.color" v-validate:required>
                <p class="uk-form-help-block uk-text-danger" v-show="form.color.invalid">
                    {{ 'Color (hex values) cannot be blank.' | trans }}</p>
            </div>
            <div class="uk-form-row">
                <input class="uk-width-1-1 uk-form-large" type="text" name="icon" :placeholder="'Category Icon' | trans"
                       v-model="category.icon" v-validate:required>
                <p class="uk-form-help-block uk-text-danger" v-show="form.icon.invalid">
                    {{ 'Icon (https://getuikit.com/docs/icon#library) cannot be blank.' | trans }}</p>
            </div>
        </div>
    </div>

</template>

<script>

    module.exports = {

        props: ['category', 'data', 'form'],

        section: {
            label: 'Category'
        }

    };

</script>

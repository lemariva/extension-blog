# Blog Extension

This extension has implemented Categories and Tags in a very simple way. If you want to check how this extension work just go to: http://frontbackend.com

It allows you to change post url from /blog/post-url to /category-name/post-url. 

## Installation

1. Download extension

a) when you don't have blog extension:
- git clone https://github.com/lemariva/extension-blog.git
- copy extension to your packages directory
- activate extension from pagekit admin panel


b) when you already have blog extension:

- remove your packages/pagekit/blog directory with all subdirs
- git clone https://github.com/lemariva/extension-blog.git
- copy new extension to the blog directory
- run scripts from DDL.sql file:

```SQL
ALTER TABLE pk_blog_post ADD tag1 VARCHAR(100);
ALTER TABLE pk_blog_post ADD tag2 VARCHAR(100);
ALTER TABLE pk_blog_post ADD tag3 VARCHAR(100);
ALTER TABLE pk_blog_post ADD tag4 VARCHAR(100);
ALTER TABLE pk_blog_post ADD tag5 VARCHAR(100);
ALTER TABLE pk_blog_post ADD tag6 VARCHAR(100);
ALTER TABLE pk_blog_post ADD tag7 VARCHAR(100);
ALTER TABLE pk_blog_post ADD tag8 VARCHAR(100);
ALTER TABLE pk_blog_post ADD tag9 VARCHAR(100);
ALTER TABLE pk_blog_post ADD tag10 VARCHAR(100);

ALTER TABLE pk_blog_post ADD category_id int(10) unsigned NOT NULL;

CREATE TABLE pk_blog_category (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  title varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  slug varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  color varchar(7) COLLATE utf8_unicode_ci NOT NULL,
  icon varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY pk_BLOG_POST_CATEGORY_SLUG (slug)
) ENGINE=InnoDB;
```

2. Add method to your UrlProvider (`app\modules\application\src\Application\UrlProvider.php`):
```php
 public function getFirstURLPath()
 {
    $request = $this->router->getRequest();
    return explode('/', $request->getPathInfo())[1];
 }
```

3. Use node.js & webpack to create the JavaScript files under `blog\app\bundle`
- install node.js from https://nodejs.org/en/
- install webpack: `npm install webpack -g`
- install vue:`npm install vue -g`
- install vue-cli: `npm install vue-cli -g`
- git clone https://github.com/lemariva/extension-blog.git
- go to the directory extension-blog
- type: `npm install` & wait
- type: `webpack` -> this creates the directory app\bundle

In your administration panel you will see new Categories tab, with list of stored categories. 

Edit category form:
![Category Form](https://github.com/martinwojtus/extension-blog/blob/master/category-form.png)

Edit post form:
![Post Form](https://github.com/martinwojtus/extension-blog/blob/master/post-edit-form.png)


Use at your own risk :)
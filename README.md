ImageCacher
============

A tiny PHP class allowing to cache locally remote images.

Usage example
-------------

```php
$ic = new ImageCacher('-7 days', 'http://mywebsite.com', 'Application/Cache/img');
echo <img src="'.$ic->getImage('imageURL').'">;
```

In this example, the class will try to download the picture from the **imageURL** and copy it in the **Application/Cache/img** folder if the image doesn't already exist.

If the image already exists and if the image was downloaded 7 days ago, the class will try to compare the two images. If they are the same, the local image will be returned. If they aren't, the local image will be updated before to be returned.

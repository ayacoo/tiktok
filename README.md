# TYPO3 Extension tiktok

## 1 Features

* Tiktok videos can be created as a file in the TYPO3 file list
* Tiktok videos can be used and output with the text with media element

## 2 Usage

### 2.1 Installation

#### Installation using Composer

The recommended way to install the extension is using Composer.

Run the following command within your [Composer][1] based TYPO3 project:

```
composer require ayacoo/tiktok
```

### 2.2 Hints

#### Emojis

For better compatibility, emojis are removed from the title or description.

#### Output

For the output, the HTML is used directly from [Tiktok][4].

#### SQL changes

In order not to have to access the oEmbed interface permanently, four fields are added to the sys_file_metadata table

### 2.3 Backend preview

In the backend, the preview is used by TextMediaRenderer. For online media, this only displays the provider's icon, in this case tiktok. If you want to display the thumbnail, for example, you need your own renderer that overwrites Textmedia. An example renderer is available in the project. Caution: This overwrites all text media elements, so only use this renderer as a basis.

You register a renderer in the TCA `Configuration/TCA/tt_content.php` with `$GLOBALS['TCA']['tt_content']['types']['textmedia']['previewRenderer'] = \Ayacoo\Tiktok\Rendering\TiktokPreviewRenderer::class;`

Documentation: https://docs.typo3.org/m/typo3/reference-coreapi/main/en-us/ApiOverview/ContentElements/CustomBackendPreview.html

## 3 Administration corner

### 3.1 Versions and support

| Tiktok | TYPO3       | PHP       | Support / Development                |
|--------|-------------|-----------|--------------------------------------|
| 2.x    | 12.x        | 8.1       | features, bugfixes, security updates |
| 1.x    | 10.x - 11.x | 7.4 - 8.0 | bugfixes, security updates           |

### 3.2 Release management

tiktok uses [**semantic versioning**][2], which means, that
* **bugfix updates** (e.g. 1.0.0 => 1.0.1) just includes small bugfixes or security relevant stuff without breaking changes,
* **minor updates** (e.g. 1.0.0 => 1.1.0) includes new features and smaller tasks without breaking changes,
* and **major updates** (e.g. 1.0.0 => 2.0.0) breaking changes which can be refactorings, features or bugfixes.

### 3.3 Contribution

**Pull Requests** are gladly welcome! Nevertheless please don't forget to add an issue and connect it to your pull requests. This
is very helpful to understand what kind of issue the **PR** is going to solve.

**Bugfixes**: Please describe what kind of bug your fix solve and give us feedback how to reproduce the issue. We're going
to accept only bugfixes if we can reproduce the issue.

## 4 Thanks / Notices

- Special thanks to Georg Ringer and his [news][3] extension. A good template to build a TYPO3 extension. Here, for example, the structure of README.md is used.
- Thanks also to b13 for the [online-media-updater][5] extension. Parts of it were allowed to be included in this extension.


[1]: https://getcomposer.org/
[2]: https://semver.org/
[3]: https://github.com/georgringer/news
[4]: https://developers.tiktok.com/doc/embed-videos
[5]: https://github.com/b13/online-media-updater

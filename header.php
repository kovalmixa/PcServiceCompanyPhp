<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?php echo $pageTitle ?? 'Мой сайт'; ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <nav>
        <ul>
            <?php
            $menuItems = [
                'Главная' => 'index.php',
                'Услуги' => 'pc-configurations.php',
                'Контакты' => 'contact.php'
            ];

            foreach ($menuItems as $name => $link): ?>
                <li><a href="<?= $link ?>"><?= $name ?></a></li>
            <?php endforeach; ?>
        </ul>
    </nav>
</header>

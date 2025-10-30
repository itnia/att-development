<?php
/**
 * @var array $meta
 * @var array $sections
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
    <meta http-equiv="Content-Language" content="ru">
    <meta charset="cp1251">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $meta['title'] ?></title>
</head>
<body>
<?php
if (isset($sections['content'])) {
    echo $sections['content'];
}
?>
<?php
//dump(get_defined_vars());

//$text = 'Теперь отдых и оздоровление на живописном берегу озера Нарочь стали ещё доступнее!';
//echo $text;
//$text = mb_convert_encoding($text, 'windows-1251', 'utf-8');
//echo $text;
?>



<button id="clickTest">test</button>
<script>
    document.getElementById('clickTest').addEventListener('click', function () {
        fetch('http://localhost:8800/?news')
            .then(response => {
                return response.text();
            })
            .then(data => {
                console.log(data);
            });
    });
</script>

<button id="clickTest2">testJson</button>
<div id="clickTest2Result"></div>
<script>
    document.getElementById('clickTest2').addEventListener('click', function () {
        fetch('http://localhost:8800/?json', {
            headers: {
                'Accept': 'application/json',
            }
        })
            .then(response => {
                return response.json();
            })
            .then(result => {
                console.log(result);
                document.getElementById('clickTest2Result').innerHTML = result.data.test;
            });
    });
</script>

</body>
</html>

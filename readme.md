# Данные российских ПИФов

```php
<?
	$funds = new InvestFunds();
	$funds->getFundsList(); // Список всех ПИФов
	$fund = $funds->searchFundByName('райффайзен - акции'); // Поиск ПИФа по названию
	$funds->getFundCost($fund, '12.04.2015'); // Получение цены пая по дате
	$funds->getAllFundCosts($fund); // Получение массива цен пая за весь период существования фонда
?>
```

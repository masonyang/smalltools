# smalltools(实用小工具)

### MoneyConvert.php【金额转大写】中方法调用

#### 数字金额转人民币大写
```bash
$money = '100050.23';
  
echo MoneyConvert::numbertowords(MoneyConvert::MONEY_TYPE_RMB,$money);

输入金额：100050.23。转换成大写为: 壹拾万零伍拾元贰角叁分

```

#### 数字金额转美元大写（美分表达）
```bash
$money = '100050.23';
  
echo MoneyConvert::numbertowords(MoneyConvert::MONEY_TYPE_DOLLAR,$money,'cents');

输入金额：100050.23。转换成大写为: ONE HUNDRED THOUSAND,FIFTY AND CENTS TWENTY-THREE ONLY【美分表达(数字转换到文字)】

```

#### 数字金额转美元大写（美点表达）
```bash
$money = '100050.23';
  
echo MoneyConvert::numbertowords(MoneyConvert::MONEY_TYPE_DOLLAR,$money,'point');

输入金额：100050.23。转换成大写为: ONE HUNDRED THOUSAND,FIFTY AND POINT TWENTY-THREE ONLY【美点表达(拼出大写字母)】


```

#### 数字金额转美元大写（分数表达法）
```bash
$money = '100050.23';
  
echo MoneyConvert::numbertowords(MoneyConvert::MONEY_TYPE_DOLLAR,$money,'fraction');

输入金额：100050.23。转换成大写为: ONE HUNDRED THOUSAND,FIFTY AND TWENTY-THREE【分数表达法(只接受数字)】


```
# Amazon Linux AMI のMySQL初期設定

<p style='text-align: right;'> &copy; 20211110 by Takanori Shima </p>

```
* 以下、Cloud9上でターミナルを起動してコマンドを打つ *
```

## 1. ワークスペースがAmazon Linux AMIで作成されているか確認
```
cat /etc/os-release
```

結果例
```
NAME="Amazon Linux AMI"
VERSION="2018.03"
ID="amzn"
ID_LIKE="rhel fedora"
VERSION_ID="2018.03"
PRETTY_NAME="Amazon Linux AMI 2018.03"
ANSI_COLOR="0;33"
CPE_NAME="cpe:/o:amazon:linux:2018.03:ga"
HOME_URL="http://aws.amazon.com/amazon-linux-ami/"
```

## 2. PHPバージョンの7系へのアップグレード

### PHPバージョン確認
```
php -v
```
> PHP 5.6.40 (cli) (built: Oct 31 2019 20:35:16) 

### PHP 7.2のインストールとアップグレード
```
sudo yum -y install http://rpms.famillecollet.com/enterprise/remi-release-6.rpm
sudo yum -y install php72 php72-cli php72-common php72-devel php72-gd php72-intl php72-mbstring php72-mysqlnd php72-pdo php72-pecl-mcrypt php72-opcache php72-pecl-apcu php72-pecl-imagick php72-pecl-memcached php72-php-pecl-redis php72-php-pecl-xdebug php72-xml
sudo alternatives --set php /usr/bin/php-7.2
php -v
```
> PHP 7.2.34 (cli) (built: Oct 21 2020 19:52:01) ( NTS )

### PDOがインストールされているか確認
```
php -m | grep pdo
```
> pdo_mysql <br>
> pdo_sqlite

## 3. システム時間をJSTに変更
```
date
```
> Tue Aug 24 03:29:41 UTC 2021

```
echo "Asia/Tokyo" | sudo tee /etc/timezone
sudo mysql_tzinfo_to_sql /usr/share/zoneinfo
sudo cp /etc/sysconfig/clock /etc/sysconfig/clock.org
```

### vi エディタ起動 使い方 https://eng-entrance.com/linux-vi-base
```
sudo vi /etc/sysconfig/clock
```

### 以下の行を書き換え上書き保存
```
ZONE="Asia/Tokyo"
```
### リンクを張る

```
sudo ln -sf /usr/share/zoneinfo/Asia/Tokyo /etc/localtime
```

### Cloud9再起動
```
sudo reboot
date
```

> Tue Aug 24 12:34:10 JST 2021

## 4. MySQLの初期設定
### MySQLの日本語文字化け設定
```
sed -e "/utf8/d" -e "/client/d" -e "/^\[mysqld_safe\]$/i character-set-server=utf8\n\n[client]\ndefault-character-set=utf8" /etc/my.cnf |sudo tee /etc/my.cnf
```

### MySQL起動
```
sudo service mysqld start
```

### MySQLに管理者(root)でログイン
```
mysql -u root
```

### 文字コード設定確認
```
mysql> show variables like '%char%';
+--------------------------+----------------------------+
| Variable_name            | Value                      |
+--------------------------+----------------------------+
| character_set_client     | utf8                       |
| character_set_connection | utf8                       |
| character_set_database   | utf8                       |
| character_set_filesystem | binary                     |
| character_set_results    | utf8                       |
| character_set_server     | utf8                       |
| character_set_system     | utf8                       |
| character_sets_dir       | /usr/share/mysql/charsets/ |
-+--------------------------+----------------------------+
```

### MySQLからログアウト
```
mysql> exit;
```

## 5. MySQLにデータベース作成
```
sudo service mysqld start
mysql -u root
mysql> show databases;
mysql> create database bbs_basic_mvc default character set utf8;
mysql> show databases;
```

## 6. MySQLにテーブル作成、ダミーデータの挿入
```
mysql> use bbs_basic_mvc;
mysql> source dump.sql;
mysql> show tables;
mysql> desc messages;
mysql> select * from messages;
```

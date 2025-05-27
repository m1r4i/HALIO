# HAL大阪教務室在室システム  
稼働中のもの: https://m1rr.info/HALIO  

著作権の関係上 /HALIO/assets/fonts の中身は空にしております。  

## データベースのセットアップ
--
-- テーブルの構造 `onlines`
--
  
```
CREATE TABLE `onlines` (
  `EmployeeNumber` int(11) NOT NULL,
  `Name` text NOT NULL,
  `type` int(11) NOT NULL COMMENT '0:teacher, 1:inst',
  `course` int(11) NOT NULL,
  `status` int(11) NOT NULL COMMENT '0:off, 1:on-desk, 2:on-school, 3:out',
  `back` text NOT NULL,
  `leave` text NOT NULL,
  `lastUpdate` text NOT NULL,
  `prevwork` text NOT NULL,
  `comment` text NOT NULL,
  `call_id` text NOT NULL,
  `locX` text NOT NULL,
  `locY` text NOT NULL,
  `score` int(11) NOT NULL COMMENT '1 - 100, 0: disable'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci; 
```
  
--
-- テーブルのインデックス `onlines`
--

```
ALTER TABLE `onlines`
  ADD PRIMARY KEY (`EmployeeNumber`),
  ADD UNIQUE KEY `EmployeeNumber` (`EmployeeNumber`);
COMMIT;
```
  
--
-- テーブルの構造 `log`
--
  
```
CREATE TABLE `log` (
  `EmployeeNumber` int(11) NOT NULL,
  `type` int(11) NOT NULL COMMENT '0: in, 1: out',
  `date` date NOT NULL,
  `time` time NOT NULL,
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
```

--
-- テーブルのインデックス `log`
--
  
```
ALTER TABLE `log`
  ADD PRIMARY KEY (`id`);
```

--
-- テーブルの AUTO_INCREMENT `log`
--
  
```
ALTER TABLE `log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25012;
COMMIT;
```

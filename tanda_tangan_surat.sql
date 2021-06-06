/*
 Navicat Premium Data Transfer

 Source Server         : Local
 Source Server Type    : MySQL
 Source Server Version : 100418
 Source Host           : localhost:3306
 Source Schema         : opensid

 Target Server Type    : MySQL
 Target Server Version : 100418
 File Encoding         : 65001

 Date: 05/06/2021 10:26:06
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for tanda_tangan_surat
-- ----------------------------
DROP TABLE IF EXISTS `tanda_tangan_surat`;
CREATE TABLE `tanda_tangan_surat`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_format_surat` int NOT NULL,
  `id_pend` int NULL DEFAULT NULL,
  `id_pamong` int NOT NULL,
  `id_user` int NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp,
  `bulan` varchar(2) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `tahun` varchar(4) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `no_surat` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `nama_surat` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `lampiran` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `nik_non_warga` decimal(16, 0) NULL DEFAULT NULL,
  `nama_non_warga` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `keterangan` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `status` char(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `file_surat` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `file_pem` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `file_spesimen` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'spesimen.png',
  `file_signed` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 27 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tanda_tangan_surat
-- ----------------------------
INSERT INTO `tanda_tangan_surat` VALUES (1, 2, 1, 14, 1, '2019-03-30 10:36:04', '03', '2019', '1', 'surat_ket_penduduk_5201142005716996_2019-03-30_1.rtf', '', NULL, NULL, 'tes', '0', 'surat_ket_pengantar_5201140706966997_2021-05-24_4.pdf', NULL, 'spesimen.png', NULL);
INSERT INTO `tanda_tangan_surat` VALUES (10, 1, 2, 14, 1, '2021-05-24 14:25:11', '05', '2021', '4', 'surat_ket_pengantar_5201140706966997_2021-05-24_4.rtf', '', NULL, NULL, 'melamar pekerjaan', '0', 'surat_ket_pengantar_5201140706966997_2021-05-24_4.pdf', NULL, 'spesimen.png', NULL);
INSERT INTO `tanda_tangan_surat` VALUES (23, 1, 20, 14, 1, '2021-05-24 12:22:38', '05', '2021', '2', 'surat_ket_pengantar_3275014601977005_2021-05-24_2.rtf', '', NULL, NULL, 'melamar pekerjaan', '0', 'surat_ket_pengantar_5201140706966997_2021-05-24_4.pdf', NULL, 'spesimen.png', NULL);
INSERT INTO `tanda_tangan_surat` VALUES (24, 1, 20, 14, 1, '2021-05-24 12:09:24', '05', '2021', '1', 'surat_ket_pengantar_3275014601977005_2021-05-24_1.rtf', '', NULL, NULL, 'ok', '0', 'surat_ket_pengantar_5201140706966997_2021-05-24_4.pdf', NULL, 'spesimen.png', NULL);
INSERT INTO `tanda_tangan_surat` VALUES (26, 15, 1, 14, 1, '2021-05-31 11:12:43', '05', '2021', '2', 'surat_ket_usaha_5201142005716996_2021-05-31_2.rtf', '', NULL, NULL, 'ternak lele', '0', 'surat_ket_usaha_52011420057169__sid__zaGRmgD.pdf', NULL, 'spesimen.png', NULL);

SET FOREIGN_KEY_CHECKS = 1;

SELECT COUNT(*) AS recCount FROM 
    (SELECT * FROM smartdb.sm18_impairment WHERE BIN_CODE IN 
        (SELECT pkID FROM 
            ( SELECT vt1.stID AS stID1, vt1.fpID AS fpID1, vt2.stID AS stID2, vt2.fpID AS fp2, 'Conflict' AS gcat, vt1.pkID AS pkID1, vt2.pkID AS pkID2 FROM 
                (SELECT BIN_CODE AS stID, fingerprint AS fpID, BIN_CODE AS pkID FROM smartdb.sm18_impairment WHERE isType='b2r' AND stkm_id=1 GROUP BY DSTRCT_CODE, WHOUSE_ID, BIN_CODE, fingerprint ORDER BY BIN_CODE) AS vt1, 
                (SELECT BIN_CODE AS stID, fingerprint AS fpID, BIN_CODE AS pkID FROM smartdb.sm18_impairment WHERE isType='b2r' AND stkm_id=2 GROUP BY DSTRCT_CODE, WHOUSE_ID, BIN_CODE, fingerprint ORDER BY BIN_CODE) AS vt2 
            WHERE vt1.stID = vt2.stID 
            AND vt1.fpID IS NOT NULL 
            AND vt2.fpID IS NOT NULL 
            AND vt1.fpID <> vt2.fpID) AS vtFull 
        ) 
    AND stkm_id=1) AS vtBase
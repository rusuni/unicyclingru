
UPDATE #__jd_store set referenceTable=CONCAT('#__', referenceTable) WHERE substring(referenceTable, 1, 3) != '#__';
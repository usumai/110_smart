<?php 

function exportISActivity($activityID) {

	$activity=array();
	
	$activityRes=qget(" 
			SELECT * 
			FROM smartdb.sm13_stk
			WHERE 
				stkm_id=$activityID");
					
	if(count($activityRes)>0)
	{
		$activity=$activityRes[0];
		$imps =qget("

    		SELECT imp.*, cat.resAbbr 
    		FROM 
				smartdb.sm18_impairment imp 
				left join 
				smartdb.sm19_result_cats cat
				on imp.findingID = cast(cat.findingID as CHAR)
    		WHERE 
    			stkm_id=$activityID
				AND ((date(delete_date) IS NULL) OR (date(delete_date)='0000-00-00'))
		");
    			  
    	if(count($imps)>0){
    		$activity["impairments"]=$imps;
    		$activity["rc_totalsent"]=count($imps);
    	}else{
    		$activity["impairments"]=array();
    		$activity["rc_totalsent"]=0;
    	}
    		  
	};
	return $activity;
}
?>
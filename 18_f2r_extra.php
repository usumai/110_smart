<?php include "01_dbcon.php"; ?>
<?php include "02_header.php"; ?>
<?php include "03_menu.php"; ?>

<?php

$auto_storageID = $_GET["auto_storageID"];
$BIN_CODE = $_GET["BIN_CODE"];

?>

<script type="text/javascript">
let ans = {
    1 : "",
    2 : "",
    3 : "",
    4 : "",
    5 : "",
    6 : "",
    7 : "",
    8 : "",
    9 : "",
}

let qns = {
    1:{
        name:   "Is the item a commonwealth asset?",
        yesPath:"2",
        noPath: "nstr",
    },
    2:{
        name:   "Is the item serial tracked?",
        yesPath:"4",
        noPath: "3",
    },
    3:{
        name:   "Does serial no. exists in WHS on MILIS?",
        yesPath:"4",
        noPath: "nstr",
    },
    4:{
        name:   "Check dues in/out status?",
        yesPath:"nstr",
        noPath: "5",
    },
    5:{
        name:   "Does it belong in a SCA?",
        yesPath:"nstr",
        noPath: "6",
    },
    6:{
        name:   "Does the item belong in this WHS?",
        yesPath:"7",
        noPath: "nstr",
    },
    7:{
        name:   "Verify inventory category. Does it fall under an exclusion list?",
        yesPath:"nstr",
        noPath: "8",
    },
    8:{
        name:   "Are there other MILIS bins in the warehouse that contain this item/stockcode?",
        yesPath:"9",
        noPath: "nstr",
    },
    9:{
        name:   "Is the physical SOH different to 1RB?",
        yesPath:"LE",
        noPath: "FF",
    },
}
// console.log(qns)
let path
$(document).ready(function() {
    
    setPage()


    function assessQuestion(qno){
        path+="<br>Question: "+qns[qno]['name']
        if (ans[qno]){
            path+="<br>Answer: "+ans[qno]
            nextStep = ans[qno]=="Yes" ? qns[qno]['yesPath'] : qns[qno]['noPath']
        }else{
            let btnYes = "<button type='button' class='btn btn-success question' value='Yes' data-qno='"+qno+"'>Yes</button>"
            let btnNo = "<button type='button' class='btn btn-danger question' value='No' data-qno='"+qno+"'>No</button>"
            path+="<br>Answer: Ability to answer"+btnYes+btnNo
            nextStep = "waiting"
        }
        return nextStep
    }



    function setPage(){
        path='';
        $('.question').toggle(false);
        console.log(ans)
        

        isFinished = false
        nextStep = 1
        do {
            nextStep = assessQuestion(nextStep)
            
            if (nextStep=='nstr'||nextStep=='LE'||nextStep=='FF'||nextStep=='waiting'){
                isFinished = true
            }
        }
        while ( isFinished == false);
        
        if (nextStep!='waiting'){
            path+="<br><br>Final result: "+nextStep
        }
        $("#test").html(path);
    }


    $('body').on('click', '.question', function() {
        let answer = $(this).val();
        let qno = $(this).data('qno');
        ans[qno] = answer;
        
        setPage()
    })


    // $("#q1").toggle(ans[q1]=='q1n');

});
</script>

<br><br>

<div class='container-fluid'>

<div class='row'>
    <div class='col'>
        <h1 class='display-4'>Extra stockcode investigation</h1>
    </div>
</div>

<div class='row'>
    <div class='col-6 lead' id='menuleft'>
    <div id='test'></div>



        <ul class="list-group list-group-flush text-center">

<li class="list-group-item question q1"><b>Is the item a commonwealth asset?</b></li>
<button class="list-group-item list-group-item-action list-group-item-success question q1 q1n" value='end' data-qno='1'>No</button>
<button class="list-group-item list-group-item-action list-group-item-danger question q1 q1y" value='2' data-qno='1'>Yes</button>

<li class="list-group-item question q2"><b>Is the item serial tracked?</b></li>
<button class="list-group-item list-group-item-action list-group-item-success question q2" value='3' data-qno='2'>No</button>
<button class="list-group-item list-group-item-action list-group-item-danger question q2" value='4' data-qno='2'>Yes</button>

<li class="list-group-item question q3"><b>Does serial no. exists in WHS on MILIS?</b></li>
<button class="list-group-item list-group-item-action list-group-item-success question q3" value='end' data-qno='3'>No</button>
<button class="list-group-item list-group-item-action list-group-item-danger question q3" value='4' data-qno='3'>Yes</button>

<li class="list-group-item question q4"><b>Check dues in/out status?</b></li>
<button class="list-group-item list-group-item-action list-group-item-success question q4" value='5'>No</button>
<button class="list-group-item list-group-item-action list-group-item-danger question q4" value='end'>Yes</button>

<li class="list-group-item question q5"><b>Does it belong in a SCA?</b></li>
<button class="list-group-item list-group-item-action list-group-item-success question q5" value='6'>No</button>
<button class="list-group-item list-group-item-action list-group-item-danger question q5" value='end'>Yes</button>

<li class="list-group-item question q6"><b>Does the item belong in this WHS?</b></li>
<button class="list-group-item list-group-item-action list-group-item-success question q6" value='end'>No</button>
<button class="list-group-item list-group-item-action list-group-item-danger question q6" value='7'>Yes</button>

<li class="list-group-item question q7"><b>Verify inventory category. Does it fall under an exclusion list?</b></li>
<button class="list-group-item list-group-item-action list-group-item-success question q7" value='8'>No</button>
<button class="list-group-item list-group-item-action list-group-item-danger question q7" value='end'>Yes</button>

<li class="list-group-item question q8"><b>Are there other MILIS bins in the warehouse that contain this item/stockcode?</b></li>
<button class="list-group-item list-group-item-action list-group-item-success question q8" value='end'>No</button>
<button class="list-group-item list-group-item-action list-group-item-danger question q8" value='9'>Yes</button>

<li class="list-group-item question q9"><b>Is the physical SOH different to 1RB?</b></li>
<button class="list-group-item list-group-item-action list-group-item-success question q9" value='FF'>No</button>
<button class="list-group-item list-group-item-action list-group-item-danger question q9" value='LE'>Yes</button>

        </ul>
    </div>
</div>



<?php include "04_footer.php"; ?>
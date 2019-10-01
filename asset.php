<?php include "01_dbcon.php"; ?>
<script src="includes/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.js"></script>
<script src="includes/vuejs-datepicker.min.js"></script>
<link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900" rel="stylesheet">
  <link href="includes/materialdesignicons.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.min.css" rel="stylesheet">
<?php
$stkm_id	= 1;

$sql = "SELECT * FROM smartdb.sm14_ass WHERE stkm_id = $stkm_id; ";
$arrsql = $arr = array();
$result = $con->query($sql);
if ($result->num_rows > 0) {
 while($r = $result->fetch_assoc()) {
     $arrsql[] = $r;
}}
$arr["assets"] = $arrsql;
$jsonarr = json_encode($arr);
?>

<br><br>

<div id="app">
  <v-app id="inspire">
    <v-card>
      <v-card-title>
        Nutrition
        <div class="flex-grow-1"></div>
        <v-text-field
          v-model="search"
          append-icon="search"
          label="Search"
          single-line
          hide-details
        ></v-text-field>
      </v-card-title>
      <v-data-table
        :headers="headers"
        :items="assets"
        :search="search"
      ></v-data-table>
    </v-card>
  </v-app>
</div>



<br><br><br><br><br><br><br><br><br><br><br>


<script>
let arr = <?=$jsonarr?>;
console.log(arr)


// var example1 = new Vue({
//   el: '#example-1',
//   data: arr
// })

new Vue({
  el: '#app',
  vuetify: new Vuetify(),
  data () {
    return {
      search: '',
      headers: [
        { text: 'Asset description', value: 'AssetDesc1' },
        { text: 'Asset description 2', value: 'AssetDesc2' },
      ],
      assets: arr["assets"],
    }
  },
})
</script>

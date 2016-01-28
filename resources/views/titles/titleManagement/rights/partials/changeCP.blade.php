<div id="CPPLEditor">
    <div class="panel panel-default">
        <div class="panel-heading" id="actionType">
			You are now acting as a <span class="proxBold"><b>Content Provider</b></span> - <a class="text-primary cp" onclick="changeCPPL('Store', {{ $film->id }})">Change to Store</a>.
        </div>
    </div>
</div>

<div id="DealContent">
    <input type="hidden" name="pid" value="deals">
    <ul class="nav nav-tabs dealTabs">
        <li class="active"><a href="#tab-basic" data-toggle="tab" class="basic" onclick="clickTab('basic');">Rental Information</a></li>
        <li><a href="#tab-addCountries" data-toggle="tab" class="addCountries" onclick="clickTab('addCountries');">Manage Regions</a></li>
        <li><a href="#tab-countriesPrices" data-toggle="tab" class="countriesPrices" onclick="clickTab('countriesPrices');">Edit Prices</a></li>
        <li><a href="#tab-contentProvider" data-toggle="tab" class="contentProvider" onclick="clickTab('contentProvider');">Revenue Sharing</a></li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane active" id="tab-basic"> <div class="miniwell">
                <form id="dealInfo">
                    <div class="form-group row"></div>
                    <div class="form-group row rentalDur">
                        <label class="col-lg-2" for="form-lease_duration">Rental Duration</label>
                        <div class="col-lg-3"><input type="text" class="form-control" id="form-lease_duration" name="lease_duration" placeholder="" value="{{ $film->lease_duration }}">
                            <span>Hours</span>
                        </div>
                    </div>
                    <div class="row">
                    </div>
                    <input type="hidden" name="act" value="saveDealInfo">
                    <input type="hidden" name="film_id" value="2505">
                    <input type="hidden" name="deal_id" value="10697">
                </form>
            </div></div>
        <div class="tab-pane" id="tab-addCountries">
            <div class="input-group  panel panel-default w318">
                <select class="form-control" id="geoTemplates">
                    <option value="1">All</option><option value="2">CIS</option><option value="5">Baltic</option><option value="6">French Speaking Countries</option><option value="7">Ukraine</option><option value="10">Baltics</option><option value="12">United Kingdom</option><option value="13">World</option><option value="15"> cis</option><option value="17">South America</option>
                </select>
            </div>

            <form name="addCountries" id="addCountries" role="form">
                <input type="hidden" name="deal_id" value="10697">
                <input type="hidden" name="act" value="load-new-geoTemplate">
                <div id="TransferContainer" class="bootstrap-transfer-container"><div class="input-group  panel panel-default w291 pull-left">                <span class="input-group-btn">                     <span class="btn glyphicon glyphicon-search"></span>                </span>                <input type="text" class="filter-input form-control">            </div>            <div style="width:10%;float:left;">&nbsp;</div>             <div class="input-group  panel panel-default w291 pull-left">                <span class="input-group-btn">                     <span class="btn glyphicon glyphicon-search"></span>                </span>                <input type="text" class="filter-input-target form-control">            </div>            <div style="clear:both;">&nbsp;</div>            <div class="selector-available  panel panel-default">                    <select multiple="multiple" class="filtered remaining changebut" style="height: 15em;"><option class="true" value="8">Netherlands Antilles</option><option class="true" value="9">Angola</option><option class="true" value="10">Antarctica</option><option class="true" value="11">Argentina</option><option class="true" value="12">American Samoa</option><option class="true" value="13">Austria</option><option class="true" value="14">Australia</option><option class="true" value="15">Aruba</option><option class="true" value="16">Azerbaijan</option><option class="true" value="17">Bosnia and Herzegovina</option><option class="true" value="18">Barbados</option><option class="true" value="19">Bangladesh</option><option class="true" value="20">Belgium</option><option class="true" value="21">Burkina Faso</option><option class="true" value="22">Bulgaria</option><option class="true" value="23">Bahrain</option><option class="true" value="24">Burundi</option><option class="true" value="25">Benin</option><option class="true" value="26">Bermuda</option><option class="true" value="27">Brunei Darussalam</option><option class="true" value="28">Bolivia</option><option class="true" value="29">Brazil</option><option class="true" value="30">Bahamas</option><option class="true" value="31">Bhutan</option><option class="true" value="32">Bouvet Island</option><option class="true" value="33">Botswana</option><option class="true" value="34">Belarus</option><option class="true" value="35">Belize</option><option class="true" value="36">Canada</option><option class="true" value="37">Cocos (Keeling) Islands</option><option class="true" value="38">Congo, The Democratic Republic</option><option class="true" value="39">Central African Republic</option><option class="true" value="40">Congo</option><option class="true" value="41">Switzerland</option><option class="true" value="42">Cote D'Ivoire</option><option class="true" value="43">Cook Islands</option><option class="true" value="44">Chile</option><option class="true" value="45">Cameroon</option><option class="true" value="46">China</option><option class="true" value="47">Colombia</option><option class="true" value="48">Costa Rica</option><option class="true" value="49">Cuba</option><option class="true" value="50">Cape Verde</option><option class="true" value="51">Christmas Island</option><option class="true" value="52">Cyprus</option><option class="true" value="53">Czech Republic</option><option class="true" value="54">Germany</option><option class="true" value="55">Djibouti</option><option class="true" value="56">Denmark</option><option class="true" value="57">Dominica</option><option class="true" value="58">Dominican Republic</option><option class="true" value="59">Algeria</option><option class="true" value="60">Ecuador</option><option class="true" value="61">Estonia</option><option class="true" value="62">Egypt</option><option class="true" value="63">Western Sahara</option><option class="true" value="64">Eritrea</option><option class="true" value="65">Spain</option><option class="true" value="66">Ethiopia</option><option class="true" value="67">Finland</option><option class="true" value="68">Fiji</option><option class="true" value="69">Falkland Islands (Malvinas)</option><option class="true" value="70">Micronesia, Federated States o</option><option class="true" value="71">Faroe Islands</option><option class="true" value="72">France</option><option class="true" value="73">France, Metropolitan</option><option class="true" value="74">Gabon</option><option class="true" value="75">United Kingdom</option><option class="true" value="76">Grenada</option><option class="true" value="77">Georgia</option><option class="true" value="78">French Guiana</option><option class="true" value="79">Ghana</option><option class="true" value="80">Gibraltar</option><option class="true" value="81">Greenland</option><option class="true" value="82">Gambia</option><option class="true" value="83">Guinea</option><option class="true" value="84">Guadeloupe</option><option class="true" value="85">Equatorial Guinea</option><option class="true" value="86">Greece</option><option class="true" value="87">South Georgia and the South Sa</option><option class="true" value="88">Guatemala</option><option class="true" value="89">Guam</option><option class="true" value="90">Guinea-Bissau</option><option class="true" value="91">Guyana</option><option class="true" value="92">Hong Kong</option><option class="true" value="93">Heard Island and McDonald Isla</option><option class="true" value="94">Honduras</option><option class="true" value="95">Croatia</option><option class="true" value="96">Haiti</option><option class="true" value="97">Hungary</option><option class="true" value="98">Indonesia</option><option class="true" value="99">Ireland</option><option class="true" value="100">Israel</option><option class="true" value="101">India</option><option class="true" value="102">British Indian Ocean Territory</option><option class="true" value="103">Iraq</option><option class="true" value="104">Iran, Islamic Republic of</option><option class="true" value="105">Iceland</option><option class="true" value="106">Italy</option><option class="true" value="107">Jamaica</option><option class="true" value="108">Jordan</option><option class="true" value="109">Japan</option><option class="true" value="110">Kenya</option><option class="true" value="111">Kyrgyzstan</option><option class="true" value="112">Cambodia</option><option class="true" value="113">Kiribati</option><option class="true" value="114">Comoros</option><option class="true" value="115">Saint Kitts and Nevis</option><option class="true" value="116">Korea, Democratic People's Rep</option><option class="true" value="117">Korea, Republic of</option><option class="true" value="118">Kuwait</option><option class="true" value="119">Cayman Islands</option><option class="true" value="120">Kazakstan</option><option class="true" value="121">Lao People's Democratic Republ</option><option class="true" value="122">Lebanon</option><option class="true" value="123">Saint Lucia</option><option class="true" value="124">Liechtenstein</option><option class="true" value="125">Sri Lanka</option><option class="true" value="126">Liberia</option><option class="true" value="127">Lesotho</option><option class="true" value="128">Lithuania</option><option class="true" value="129">Luxembourg</option><option class="true" value="130">Latvia</option><option class="true" value="131">Libyan Arab Jamahiriya</option><option class="true" value="132">Morocco</option><option class="true" value="133">Monaco</option><option class="true" value="134">Moldova, Republic of</option><option class="true" value="135">Madagascar</option><option class="true" value="136">Marshall Islands</option><option class="true" value="137">Macedonia</option><option class="true" value="138">Mali</option><option class="true" value="139">Myanmar</option><option class="true" value="140">Mongolia</option><option class="true" value="141">Macau</option><option class="true" value="142">Northern Mariana Islands</option><option class="true" value="143">Martinique</option><option class="true" value="144">Mauritania</option><option class="true" value="145">Montserrat</option><option class="true" value="146">Malta</option><option class="true" value="147">Mauritius</option><option class="true" value="148">Maldives</option><option class="true" value="149">Malawi</option><option class="true" value="150">Mexico</option><option class="true" value="151">Malaysia</option><option class="true" value="152">Mozambique</option><option class="true" value="153">Namibia</option><option class="true" value="154">New Caledonia</option><option class="true" value="155">Niger</option><option class="true" value="156">Norfolk Island</option><option class="true" value="157">Nigeria</option><option class="true" value="158">Nicaragua</option><option class="true" value="159">Netherlands</option><option class="true" value="160">Norway</option><option class="true" value="161">Nepal</option><option class="true" value="162">Nauru</option><option class="true" value="163">Niue</option><option class="true" value="164">New Zealand</option><option class="true" value="165">Oman</option><option class="true" value="166">Panama</option><option class="true" value="167">Peru</option><option class="true" value="168">French Polynesia</option><option class="true" value="169">Papua New Guinea</option><option class="true" value="170">Philippines</option><option class="true" value="171">Pakistan</option><option class="true" value="172">Poland</option><option class="true" value="173">Saint Pierre and Miquelon</option><option class="true" value="174">Pitcairn Islands</option><option class="true" value="175">Puerto Rico</option><option class="true" value="176">Palestinian Territory</option><option class="true" value="177">Portugal</option><option class="true" value="178">Palau</option><option class="true" value="179">Paraguay</option><option class="true" value="180">Qatar</option><option class="true" value="181">Reunion</option><option class="true" value="182">Romania</option><option class="true" value="183">Russian Federation</option><option class="true" value="184">Rwanda</option><option class="true" value="185">Saudi Arabia</option><option class="true" value="186">Solomon Islands</option><option class="true" value="187">Seychelles</option><option class="true" value="188">Sudan</option><option class="true" value="189">Sweden</option><option class="true" value="190">Singapore</option><option class="true" value="191">Saint Helena</option><option class="true" value="192">Slovenia</option><option class="true" value="193">Svalbard and Jan Mayen</option><option class="true" value="194">Slovakia</option><option class="true" value="195">Sierra Leone</option><option class="true" value="196">San Marino</option><option class="true" value="197">Senegal</option><option class="true" value="198">Somalia</option><option class="true" value="199">Suriname</option><option class="true" value="200">Sao Tome and Principe</option><option class="true" value="201">El Salvador</option><option class="true" value="202">Syrian Arab Republic</option><option class="true" value="203">Swaziland</option><option class="true" value="204">Turks and Caicos Islands</option><option class="true" value="205">Chad</option><option class="true" value="206">French Southern Territories</option><option class="true" value="207">Togo</option><option class="true" value="208">Thailand</option><option class="true" value="209">Tajikistan</option><option class="true" value="210">Tokelau</option><option class="true" value="211">Turkmenistan</option><option class="true" value="212">Tunisia</option><option class="true" value="213">Tonga</option><option class="true" value="214">Timor-Leste</option><option class="true" value="215">Turkey</option><option class="true" value="216">Trinidad and Tobago</option><option class="true" value="217">Tuvalu</option><option class="true" value="218">Taiwan</option><option class="true" value="219">Tanzania, United Republic of</option><option class="true" value="220">Ukraine</option><option class="true" value="221">Uganda</option><option class="true" value="222">United States Minor Outlying I</option><option class="true" value="223">United States</option><option class="true" value="224">Uruguay</option><option class="true" value="225">Uzbekistan</option><option class="true" value="226">Holy See (Vatican City State)</option><option class="true" value="227">Saint Vincent and the Grenadin</option><option class="true" value="228">Venezuela</option><option class="true" value="229">Virgin Islands, British</option><option class="true" value="230">Virgin Islands, U.S.</option><option class="true" value="231">Vietnam</option><option class="true" value="232">Vanuatu</option><option class="true" value="233">Wallis and Futuna</option><option class="true" value="234">Samoa</option><option class="true" value="235">Yemen</option><option class="true" value="236">Mayotte</option><option class="true" value="237">Serbia</option><option class="true" value="238">South Africa</option><option class="true" value="239">Zambia</option><option class="true" value="240">Montenegro</option><option class="true" value="241">Zimbabwe</option><option class="true" value="242">Anonymous Proxy</option><option class="true" value="243">Satellite Provider</option><option class="true" value="244">Other</option><option class="true" value="245">Aland Islands</option><option class="true" value="246">Guernsey</option><option class="true" value="247">Isle of Man</option><option class="true" value="248">Jersey</option><option class="true" value="249">Saint Barthelemy</option><option class="true" value="250">Saint Martin</option></select>                <a href="#" class="selector-chooseall changebut">Add All </a>            </div>            <div class="selector-chooser " style="width:10%;">                <span class="selector-add glyphicon glyphicon-log-out changebut"></span>                <span href="#" class="selector-remove glyphicon glyphicon-remove changebut"></span>            </div>            <div class="selector-chosen  panel panel-default">                    <select multiple="multiple" class="filtered target" id="multi-select-input" style="height: 15em;"><option value="1">Andorra</option><option value="2">United Arab Emirates</option><option value="3">Afghanistan</option><option value="4">Antigua and Barbuda</option><option value="5">Anguilla</option><option value="6">Albania</option><option value="7">Armenia</option></select>                <a href="#" class="selector-clearall changebut">Clear All </a>            </div>                    </div>
            </form>
            <script>
                $(function() {
                    var t = $('#TransferContainer').bootstrapTransfer(
                            {'target_id': 'multi-select-input',
                                'height': '15em',
                                'hilite_selection': true});

                    t.populate([
                                {value:8, content:"Netherlands Antilles", status:true},{value:9, content:"Angola", status:true},{value:10, content:"Antarctica", status:true},{value:11, content:"Argentina", status:true},{value:12, content:"American Samoa", status:true},{value:13, content:"Austria", status:true},{value:14, content:"Australia", status:true},{value:15, content:"Aruba", status:true},{value:16, content:"Azerbaijan", status:true},{value:17, content:"Bosnia and Herzegovina", status:true},{value:18, content:"Barbados", status:true},{value:19, content:"Bangladesh", status:true},{value:20, content:"Belgium", status:true},{value:21, content:"Burkina Faso", status:true},{value:22, content:"Bulgaria", status:true},{value:23, content:"Bahrain", status:true},{value:24, content:"Burundi", status:true},{value:25, content:"Benin", status:true},{value:26, content:"Bermuda", status:true},{value:27, content:"Brunei Darussalam", status:true},{value:28, content:"Bolivia", status:true},{value:29, content:"Brazil", status:true},{value:30, content:"Bahamas", status:true},{value:31, content:"Bhutan", status:true},{value:32, content:"Bouvet Island", status:true},{value:33, content:"Botswana", status:true},{value:34, content:"Belarus", status:true},{value:35, content:"Belize", status:true},{value:36, content:"Canada", status:true},{value:37, content:"Cocos (Keeling) Islands", status:true},{value:38, content:"Congo, The Democratic Republic", status:true},{value:39, content:"Central African Republic", status:true},{value:40, content:"Congo", status:true},{value:41, content:"Switzerland", status:true},{value:42, content:"Cote D'Ivoire", status:true},{value:43, content:"Cook Islands", status:true},{value:44, content:"Chile", status:true},{value:45, content:"Cameroon", status:true},{value:46, content:"China", status:true},{value:47, content:"Colombia", status:true},{value:48, content:"Costa Rica", status:true},{value:49, content:"Cuba", status:true},{value:50, content:"Cape Verde", status:true},{value:51, content:"Christmas Island", status:true},{value:52, content:"Cyprus", status:true},{value:53, content:"Czech Republic", status:true},{value:54, content:"Germany", status:true},{value:55, content:"Djibouti", status:true},{value:56, content:"Denmark", status:true},{value:57, content:"Dominica", status:true},{value:58, content:"Dominican Republic", status:true},{value:59, content:"Algeria", status:true},{value:60, content:"Ecuador", status:true},{value:61, content:"Estonia", status:true},{value:62, content:"Egypt", status:true},{value:63, content:"Western Sahara", status:true},{value:64, content:"Eritrea", status:true},{value:65, content:"Spain", status:true},{value:66, content:"Ethiopia", status:true},{value:67, content:"Finland", status:true},{value:68, content:"Fiji", status:true},{value:69, content:"Falkland Islands (Malvinas)", status:true},{value:70, content:"Micronesia, Federated States o", status:true},{value:71, content:"Faroe Islands", status:true},{value:72, content:"France", status:true},{value:73, content:"France, Metropolitan", status:true},{value:74, content:"Gabon", status:true},{value:75, content:"United Kingdom", status:true},{value:76, content:"Grenada", status:true},{value:77, content:"Georgia", status:true},{value:78, content:"French Guiana", status:true},{value:79, content:"Ghana", status:true},{value:80, content:"Gibraltar", status:true},{value:81, content:"Greenland", status:true},{value:82, content:"Gambia", status:true},{value:83, content:"Guinea", status:true},{value:84, content:"Guadeloupe", status:true},{value:85, content:"Equatorial Guinea", status:true},{value:86, content:"Greece", status:true},{value:87, content:"South Georgia and the South Sa", status:true},{value:88, content:"Guatemala", status:true},{value:89, content:"Guam", status:true},{value:90, content:"Guinea-Bissau", status:true},{value:91, content:"Guyana", status:true},{value:92, content:"Hong Kong", status:true},{value:93, content:"Heard Island and McDonald Isla", status:true},{value:94, content:"Honduras", status:true},{value:95, content:"Croatia", status:true},{value:96, content:"Haiti", status:true},{value:97, content:"Hungary", status:true},{value:98, content:"Indonesia", status:true},{value:99, content:"Ireland", status:true},{value:100, content:"Israel", status:true},{value:101, content:"India", status:true},{value:102, content:"British Indian Ocean Territory", status:true},{value:103, content:"Iraq", status:true},{value:104, content:"Iran, Islamic Republic of", status:true},{value:105, content:"Iceland", status:true},{value:106, content:"Italy", status:true},{value:107, content:"Jamaica", status:true},{value:108, content:"Jordan", status:true},{value:109, content:"Japan", status:true},{value:110, content:"Kenya", status:true},{value:111, content:"Kyrgyzstan", status:true},{value:112, content:"Cambodia", status:true},{value:113, content:"Kiribati", status:true},{value:114, content:"Comoros", status:true},{value:115, content:"Saint Kitts and Nevis", status:true},{value:116, content:"Korea, Democratic People's Rep", status:true},{value:117, content:"Korea, Republic of", status:true},{value:118, content:"Kuwait", status:true},{value:119, content:"Cayman Islands", status:true},{value:120, content:"Kazakstan", status:true},{value:121, content:"Lao People's Democratic Republ", status:true},{value:122, content:"Lebanon", status:true},{value:123, content:"Saint Lucia", status:true},{value:124, content:"Liechtenstein", status:true},{value:125, content:"Sri Lanka", status:true},{value:126, content:"Liberia", status:true},{value:127, content:"Lesotho", status:true},{value:128, content:"Lithuania", status:true},{value:129, content:"Luxembourg", status:true},{value:130, content:"Latvia", status:true},{value:131, content:"Libyan Arab Jamahiriya", status:true},{value:132, content:"Morocco", status:true},{value:133, content:"Monaco", status:true},{value:134, content:"Moldova, Republic of", status:true},{value:135, content:"Madagascar", status:true},{value:136, content:"Marshall Islands", status:true},{value:137, content:"Macedonia", status:true},{value:138, content:"Mali", status:true},{value:139, content:"Myanmar", status:true},{value:140, content:"Mongolia", status:true},{value:141, content:"Macau", status:true},{value:142, content:"Northern Mariana Islands", status:true},{value:143, content:"Martinique", status:true},{value:144, content:"Mauritania", status:true},{value:145, content:"Montserrat", status:true},{value:146, content:"Malta", status:true},{value:147, content:"Mauritius", status:true},{value:148, content:"Maldives", status:true},{value:149, content:"Malawi", status:true},{value:150, content:"Mexico", status:true},{value:151, content:"Malaysia", status:true},{value:152, content:"Mozambique", status:true},{value:153, content:"Namibia", status:true},{value:154, content:"New Caledonia", status:true},{value:155, content:"Niger", status:true},{value:156, content:"Norfolk Island", status:true},{value:157, content:"Nigeria", status:true},{value:158, content:"Nicaragua", status:true},{value:159, content:"Netherlands", status:true},{value:160, content:"Norway", status:true},{value:161, content:"Nepal", status:true},{value:162, content:"Nauru", status:true},{value:163, content:"Niue", status:true},{value:164, content:"New Zealand", status:true},{value:165, content:"Oman", status:true},{value:166, content:"Panama", status:true},{value:167, content:"Peru", status:true},{value:168, content:"French Polynesia", status:true},{value:169, content:"Papua New Guinea", status:true},{value:170, content:"Philippines", status:true},{value:171, content:"Pakistan", status:true},{value:172, content:"Poland", status:true},{value:173, content:"Saint Pierre and Miquelon", status:true},{value:174, content:"Pitcairn Islands", status:true},{value:175, content:"Puerto Rico", status:true},{value:176, content:"Palestinian Territory", status:true},{value:177, content:"Portugal", status:true},{value:178, content:"Palau", status:true},{value:179, content:"Paraguay", status:true},{value:180, content:"Qatar", status:true},{value:181, content:"Reunion", status:true},{value:182, content:"Romania", status:true},{value:183, content:"Russian Federation", status:true},{value:184, content:"Rwanda", status:true},{value:185, content:"Saudi Arabia", status:true},{value:186, content:"Solomon Islands", status:true},{value:187, content:"Seychelles", status:true},{value:188, content:"Sudan", status:true},{value:189, content:"Sweden", status:true},{value:190, content:"Singapore", status:true},{value:191, content:"Saint Helena", status:true},{value:192, content:"Slovenia", status:true},{value:193, content:"Svalbard and Jan Mayen", status:true},{value:194, content:"Slovakia", status:true},{value:195, content:"Sierra Leone", status:true},{value:196, content:"San Marino", status:true},{value:197, content:"Senegal", status:true},{value:198, content:"Somalia", status:true},{value:199, content:"Suriname", status:true},{value:200, content:"Sao Tome and Principe", status:true},{value:201, content:"El Salvador", status:true},{value:202, content:"Syrian Arab Republic", status:true},{value:203, content:"Swaziland", status:true},{value:204, content:"Turks and Caicos Islands", status:true},{value:205, content:"Chad", status:true},{value:206, content:"French Southern Territories", status:true},{value:207, content:"Togo", status:true},{value:208, content:"Thailand", status:true},{value:209, content:"Tajikistan", status:true},{value:210, content:"Tokelau", status:true},{value:211, content:"Turkmenistan", status:true},{value:212, content:"Tunisia", status:true},{value:213, content:"Tonga", status:true},{value:214, content:"Timor-Leste", status:true},{value:215, content:"Turkey", status:true},{value:216, content:"Trinidad and Tobago", status:true},{value:217, content:"Tuvalu", status:true},{value:218, content:"Taiwan", status:true},{value:219, content:"Tanzania, United Republic of", status:true},{value:220, content:"Ukraine", status:true},{value:221, content:"Uganda", status:true},{value:222, content:"United States Minor Outlying I", status:true},{value:223, content:"United States", status:true},{value:224, content:"Uruguay", status:true},{value:225, content:"Uzbekistan", status:true},{value:226, content:"Holy See (Vatican City State)", status:true},{value:227, content:"Saint Vincent and the Grenadin", status:true},{value:228, content:"Venezuela", status:true},{value:229, content:"Virgin Islands, British", status:true},{value:230, content:"Virgin Islands, U.S.", status:true},{value:231, content:"Vietnam", status:true},{value:232, content:"Vanuatu", status:true},{value:233, content:"Wallis and Futuna", status:true},{value:234, content:"Samoa", status:true},{value:235, content:"Yemen", status:true},{value:236, content:"Mayotte", status:true},{value:237, content:"Serbia", status:true},{value:238, content:"South Africa", status:true},{value:239, content:"Zambia", status:true},{value:240, content:"Montenegro", status:true},{value:241, content:"Zimbabwe", status:true},{value:242, content:"Anonymous Proxy", status:true},{value:243, content:"Satellite Provider", status:true},{value:244, content:"Other", status:true},{value:245, content:"Aland Islands", status:true},{value:246, content:"Guernsey", status:true},{value:247, content:"Isle of Man", status:true},{value:248, content:"Jersey", status:true},{value:249, content:"Saint Barthelemy", status:true},{value:250, content:"Saint Martin", status:true},
                            ],
                            [
                                {value:1, content:"Andorra"},{value:2, content:"United Arab Emirates"},{value:3, content:"Afghanistan"},{value:4, content:"Antigua and Barbuda"},{value:5, content:"Anguilla"},{value:6, content:"Albania"},{value:7, content:"Armenia"},
                            ]
                    );

                    t.populate();
                    //t.set_values(['2', '4']);
                    //console.log(t.get_values());
                });
            </script>
        </div>
        <div class="tab-pane" id="tab-countriesPrices">
            <div class="panel panel-default">
                <div class="panel-heading">Date &amp; Price</div>
                <div class="panel-body">

                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="form-group row">
                                <label class="col-lg-2" for="price">Rent Price ($)</label>
                                <div class="col-lg-3"><input type="text" id="rent_price" name="rent_price" class="form-control" value=""></div>
                                <div class="col-lg-3"><button class="btn btn-default btn-xs allrentprice" type="button">Apply to All Countries</button></div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-2" for="buy_price_nominal">Buy Price ($)</label>
                                <div class="col-lg-3"><input type="text" id="buy_price" name="buy_price" class="form-control" value=""></div>
                                <div class="col-lg-3"><button class="btn btn-default btn-xs allbuyprice" type="button">Apply to All Countries</button></div>
                            </div>
                        </div>
                    </div>


                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="form-group row">
                                <label class="col-lg-2" for="start_date">Start Date</label>
                                <div class="col-lg-3"><input type="text" id="start_date" name="start_date" class="form-control dt hasDatepicker" value=""></div>

                            </div>
                            <div class="form-group row">
                                <label class="col-lg-2" for="end_date">End Date</label>
                                <div class="col-lg-3"><input type="text" id="end_date" name="end_date" class="form-control dt hasDatepicker" value=""></div>
                            </div>
                            <div class="row">
                                <div class="col-lg-5"><button class="btn btn-default btn-xs alldate  pull-right" type="button">Apply to All Countries</button></div>
                            </div>
                        </div>
                    </div>




                </div>
            </div>


            <script>
                $(document).ready(function(){

                    $("#start_date").datepicker();
                    $("#end_date").datepicker();
                    //$.datepicker.setDefaults({dateFormat:"yy-mm-dd",changeYear:true});
                });
                $(function(){


                    $(".alldate").click(function(){
                        var start_date = $("#start_date").val();
                        var end_date = $("#end_date").val();
                        $.ajax({
                            type: "POST",
                            url: "engine.php",
                            data: "start_date="+start_date+"&end_date="+end_date+"&act=alldate&deal_id="+deal_id+"&film_id="+film_id ,
                            dataType: "json",
                            success: function (data) {
                                reloadAll("countriesPrices");
                            }
                        });


                    });
                    $(".allrentprice").click(function(){
                        var rent_price = $("#rent_price").val();
                        $.ajax({
                            type: "POST",
                            url: "engine.php",
                            data: "rent_price="+rent_price+"&act=allrentprice&deal_id="+deal_id+"&film_id="+film_id ,
                            dataType: "json",
                            success: function (data) {
                                reloadAll("countriesPrices");
                            }
                        });


                    });


                    $(".allbuyprice").click(function(){
                        var buy_price = $("#buy_price").val();
                        $.ajax({
                            type: "POST",
                            url: "engine.php",
                            data: "buy_price="+buy_price+"&act=allbuyprice&deal_id="+deal_id+"&film_id="+film_id ,
                            dataType: "json",
                            success: function (data) {
                                reloadAll("countriesPrices");
                            }
                        });


                    });
                });
            </script>

            <div class="priceTab row">
                <a class="cp act" id="rentpriceHeader" onclick="ChangeRentBuy('rent')">Rent Price</a>
                <a class="cp" id="buypriceHeader" onclick="ChangeRentBuy('buy')">Buy Price</a>
            </div>

            <div class="countries">

                <div class="well">
                    <div class="row">
                        <p class="col-lg-3">Country name</p>
                        <div class="cInputs ctitles">
                            <p class="dt col-lg-2" id="dp1453805371997">Start&nbsp;Date</p>
                            <p class="dt col-lg-2" id="dp1453805371998">End&nbsp;Date</p>
                            <div class="pricesep">&nbsp;</div>
                            <p>USD</p>
                            <p>National</p>
                            <p>=USD</p>

                        </div>
                        <!--div class="cInputs">
                       <input type="text" class="dt" readonly="readonly" value="Start Date">
                        <input  type="text" class="dt" value="End Date" readonly="readonly" >
                        <div class="pricesep">&nbsp;</div>
                        <input class="rentprice priceHide" type="text" value="USD" readonly="readonly">
                        <input class="rentprice priceHide" type="text" value="National" readonly="readonly">
                        <input class="rentprice priceHide" type="text" class="tip" value="=USD" readonly="readonly" data-toggle="tooltip" data-placement="top"  title="Rent National">

                        <input class="buyprice" type="text" value="USD" readonly="readonly">
                        <input class="buyprice" type="text" value="National" readonly="readonly">
                        <input class="buyprice" type="text" class="tip"  value="=USD" readonly="readonly" data-toggle="tooltip" data-placement="top"  title="Buy National">
                     </div-->
                    </div>
                </div>
                <form name="contriesPrices" id="contriesPrices" role="form">
                    <div class="well">
                        <div class="row">
                            <p class="col-lg-3">Andorra</p>
                            <div class="cInputs">
                                <input type="hidden" name="ccode" value="EUR" class="ccode">
                                <input id="dp1453805371999" class="dt hasDatepicker" type="text" value="2015-08-07" name="item[136267][start]">
                                <input id="dp1453805372000" class="dt hasDatepicker" type="text" value="2017-08-07" name="item[136267][end]">
                                <div class="pricesep">&nbsp;</div>
                                <input class="rentprice  rent_price_nominal" type="text" value="2.00" name="item[136267][rent_price_nominal]">
                                <input class="rentprice  rent_price_national" type="text" value="500.00" name="item[136267][rent_price_national]">
                                <input class="rentprice  rent_price" type="text" value="540.86" name="item[136267][rent_price]" readonly="readonly">

                                <input class="buyprice buy_price_nominal priceHide" type="text" value="3.00" name="item[136267][buy_price_nominal]">
                                <input class="buyprice buy_price_national priceHide" type="text" value="0.00" name="item[136267][buy_price_national]">
                                <input class="buyprice buy_price priceHide" type="text" value="3.00" name="item[136267][buy_price]" readonly="readonly">

                                <input type="hidden" value="136267" name="item[136267][id]">
                                <div class="sr"><span class="glyphicon glyphicon-floppy-saved cp" onclick="saveCountryItem('136267')"></span></div>
                                <!--div class="sr"><span class="glyphicon glyphicon-floppy-remove cp"></span></div-->
                            </div>
                        </div>
                    </div><div class="well">
                        <div class="row">
                            <p class="col-lg-3">United Arab Emirates</p>
                            <div class="cInputs">
                                <input type="hidden" name="ccode" value="AED" class="ccode">
                                <input id="dp1453805372001" class="dt hasDatepicker" type="text" value="2015-08-07" name="item[136268][start]">
                                <input id="dp1453805372002" class="dt hasDatepicker" type="text" value="2017-08-07" name="item[136268][end]">
                                <div class="pricesep">&nbsp;</div>
                                <input class="rentprice  rent_price_nominal" type="text" value="2.00" name="item[136268][rent_price_nominal]">
                                <input class="rentprice  rent_price_national" type="text" value="0.00" name="item[136268][rent_price_national]">
                                <input class="rentprice  rent_price" type="text" value="2.00" name="item[136268][rent_price]" readonly="readonly">

                                <input class="buyprice buy_price_nominal priceHide" type="text" value="3.00" name="item[136268][buy_price_nominal]">
                                <input class="buyprice buy_price_national priceHide" type="text" value="0.00" name="item[136268][buy_price_national]">
                                <input class="buyprice buy_price priceHide" type="text" value="3.00" name="item[136268][buy_price]" readonly="readonly">

                                <input type="hidden" value="136268" name="item[136268][id]">
                                <div class="sr"><span class="glyphicon glyphicon-floppy-saved cp" onclick="saveCountryItem('136268')"></span></div>
                                <!--div class="sr"><span class="glyphicon glyphicon-floppy-remove cp"></span></div-->
                            </div>
                        </div>
                    </div><div class="well">
                        <div class="row">
                            <p class="col-lg-3">Afghanistan</p>
                            <div class="cInputs">
                                <input type="hidden" name="ccode" value="AFA" class="ccode">
                                <input id="dp1453805372003" class="dt hasDatepicker" type="text" value="2015-08-07" name="item[136269][start]">
                                <input id="dp1453805372004" class="dt hasDatepicker" type="text" value="2017-08-07" name="item[136269][end]">
                                <div class="pricesep">&nbsp;</div>
                                <input class="rentprice  rent_price_nominal" type="text" value="2.00" name="item[136269][rent_price_nominal]">
                                <input class="rentprice  rent_price_national" type="text" value="0.00" name="item[136269][rent_price_national]">
                                <input class="rentprice  rent_price" type="text" value="2.00" name="item[136269][rent_price]" readonly="readonly">

                                <input class="buyprice buy_price_nominal priceHide" type="text" value="3.00" name="item[136269][buy_price_nominal]">
                                <input class="buyprice buy_price_national priceHide" type="text" value="0.00" name="item[136269][buy_price_national]">
                                <input class="buyprice buy_price priceHide" type="text" value="3.00" name="item[136269][buy_price]" readonly="readonly">

                                <input type="hidden" value="136269" name="item[136269][id]">
                                <div class="sr"><span class="glyphicon glyphicon-floppy-saved cp" onclick="saveCountryItem('136269')"></span></div>
                                <!--div class="sr"><span class="glyphicon glyphicon-floppy-remove cp"></span></div-->
                            </div>
                        </div>
                    </div><div class="well">
                        <div class="row">
                            <p class="col-lg-3">Antigua and Barbuda</p>
                            <div class="cInputs">
                                <input type="hidden" name="ccode" value="XCD" class="ccode">
                                <input id="dp1453805372005" class="dt hasDatepicker" type="text" value="2015-08-07" name="item[136270][start]">
                                <input id="dp1453805372006" class="dt hasDatepicker" type="text" value="2017-08-07" name="item[136270][end]">
                                <div class="pricesep">&nbsp;</div>
                                <input class="rentprice  rent_price_nominal" type="text" value="2.00" name="item[136270][rent_price_nominal]">
                                <input class="rentprice  rent_price_national" type="text" value="0.00" name="item[136270][rent_price_national]">
                                <input class="rentprice  rent_price" type="text" value="2.00" name="item[136270][rent_price]" readonly="readonly">

                                <input class="buyprice buy_price_nominal priceHide" type="text" value="3.00" name="item[136270][buy_price_nominal]">
                                <input class="buyprice buy_price_national priceHide" type="text" value="0.00" name="item[136270][buy_price_national]">
                                <input class="buyprice buy_price priceHide" type="text" value="3.00" name="item[136270][buy_price]" readonly="readonly">

                                <input type="hidden" value="136270" name="item[136270][id]">
                                <div class="sr"><span class="glyphicon glyphicon-floppy-saved cp" onclick="saveCountryItem('136270')"></span></div>
                                <!--div class="sr"><span class="glyphicon glyphicon-floppy-remove cp"></span></div-->
                            </div>
                        </div>
                    </div><div class="well">
                        <div class="row">
                            <p class="col-lg-3">Anguilla</p>
                            <div class="cInputs">
                                <input type="hidden" name="ccode" value="XCD" class="ccode">
                                <input id="dp1453805372007" class="dt hasDatepicker" type="text" value="2015-08-07" name="item[136271][start]">
                                <input id="dp1453805372008" class="dt hasDatepicker" type="text" value="2017-08-07" name="item[136271][end]">
                                <div class="pricesep">&nbsp;</div>
                                <input class="rentprice  rent_price_nominal" type="text" value="2.00" name="item[136271][rent_price_nominal]">
                                <input class="rentprice  rent_price_national" type="text" value="0.00" name="item[136271][rent_price_national]">
                                <input class="rentprice  rent_price" type="text" value="2.00" name="item[136271][rent_price]" readonly="readonly">

                                <input class="buyprice buy_price_nominal priceHide" type="text" value="3.00" name="item[136271][buy_price_nominal]">
                                <input class="buyprice buy_price_national priceHide" type="text" value="0.00" name="item[136271][buy_price_national]">
                                <input class="buyprice buy_price priceHide" type="text" value="3.00" name="item[136271][buy_price]" readonly="readonly">

                                <input type="hidden" value="136271" name="item[136271][id]">
                                <div class="sr"><span class="glyphicon glyphicon-floppy-saved cp" onclick="saveCountryItem('136271')"></span></div>
                                <!--div class="sr"><span class="glyphicon glyphicon-floppy-remove cp"></span></div-->
                            </div>
                        </div>
                    </div><div class="well">
                        <div class="row">
                            <p class="col-lg-3">Albania</p>
                            <div class="cInputs">
                                <input type="hidden" name="ccode" value="ALL" class="ccode">
                                <input id="dp1453805372009" class="dt hasDatepicker" type="text" value="2015-08-07" name="item[136272][start]">
                                <input id="dp1453805372010" class="dt hasDatepicker" type="text" value="2017-08-07" name="item[136272][end]">
                                <div class="pricesep">&nbsp;</div>
                                <input class="rentprice  rent_price_nominal" type="text" value="2.00" name="item[136272][rent_price_nominal]">
                                <input class="rentprice  rent_price_national" type="text" value="0.00" name="item[136272][rent_price_national]">
                                <input class="rentprice  rent_price" type="text" value="2.00" name="item[136272][rent_price]" readonly="readonly">

                                <input class="buyprice buy_price_nominal priceHide" type="text" value="3.00" name="item[136272][buy_price_nominal]">
                                <input class="buyprice buy_price_national priceHide" type="text" value="0.00" name="item[136272][buy_price_national]">
                                <input class="buyprice buy_price priceHide" type="text" value="3.00" name="item[136272][buy_price]" readonly="readonly">

                                <input type="hidden" value="136272" name="item[136272][id]">
                                <div class="sr"><span class="glyphicon glyphicon-floppy-saved cp" onclick="saveCountryItem('136272')"></span></div>
                                <!--div class="sr"><span class="glyphicon glyphicon-floppy-remove cp"></span></div-->
                            </div>
                        </div>
                    </div><div class="well">
                        <div class="row">
                            <p class="col-lg-3">Armenia</p>
                            <div class="cInputs">
                                <input type="hidden" name="ccode" value="AMD" class="ccode">
                                <input id="dp1453805372011" class="dt hasDatepicker" type="text" value="2015-08-07" name="item[136684][start]">
                                <input id="dp1453805372012" class="dt hasDatepicker" type="text" value="2017-08-07" name="item[136684][end]">
                                <div class="pricesep">&nbsp;</div>
                                <input class="rentprice  rent_price_nominal" type="text" value="2.00" name="item[136684][rent_price_nominal]">
                                <input class="rentprice  rent_price_national" type="text" value="0.00" name="item[136684][rent_price_national]">
                                <input class="rentprice  rent_price" type="text" value="2.00" name="item[136684][rent_price]" readonly="readonly">

                                <input class="buyprice buy_price_nominal priceHide" type="text" value="3.00" name="item[136684][buy_price_nominal]">
                                <input class="buyprice buy_price_national priceHide" type="text" value="0.00" name="item[136684][buy_price_national]">
                                <input class="buyprice buy_price priceHide" type="text" value="3.00" name="item[136684][buy_price]" readonly="readonly">

                                <input type="hidden" value="136684" name="item[136684][id]">
                                <div class="sr"><span class="glyphicon glyphicon-floppy-saved cp" onclick="saveCountryItem('136684')"></span></div>
                                <!--div class="sr"><span class="glyphicon glyphicon-floppy-remove cp"></span></div-->
                            </div>
                        </div>
                    </div>
                    <input type="hidden" value="saveCountriesPrices" name="act">
                    <input type="hidden" value="10697" name="deal_id">
                </form>
            </div>


            <script>
                ChangeRentBuy(PricesTabsToOpen);
                $(function(){
                    $(".rent_price_nominal").keyup(function(){
                        priceCalc($(this).parent().children(".ccode"),$(this).parent().children(".rent_price_nominal"),$(this).parent().children(".rent_price_national"),$(this).parent().children(".rent_price"));
                    });
                    $(".rent_price_national").keyup(function(){
                        priceCalc($(this).parent().children(".ccode"),$(this).parent().children(".rent_price_nominal"),$(this).parent().children(".rent_price_national"),$(this).parent().children(".rent_price"));
                    });
                    $(".buy_price_national").keyup(function(){
                        priceCalc($(this).parent().children(".ccode"),$(this).parent().children(".buy_price_nominal"),$(this).parent().children(".buy_price_national"),$(this).parent().children(".buy_price"));
                    });
                    $(".buy_price_nominal").keyup(function(){
                        priceCalc($(this).parent().children(".ccode"),$(this).parent().children(".buy_price_nominal"),$(this).parent().children(".buy_price_national"),$(this).parent().children(".buy_price"));
                    });
                });
                $(function(){

                    $( "#buypriceHeader" ).click(function() {
                        PricesTabsToOpen = "buy";
                    });
                    $( "#rentpriceHeader" ).click(function() {
                        PricesTabsToOpen = "rent";
                    });
                });
            </script>
        </div>
        <div class="tab-pane " id="tab-contentProvider">
            <div class="miniwell">
                <form id="dealCP">
                    <div class="panel panel-default">
                        <div class="panel-heading">Revenue Sharing</div>
                        <div class="panel-body">
                            <div class="form-group row">
                                <label class="col-lg-2" for="bprice">Model</label>
                                <div class="col-lg-3">
                                    <select class="form-control" name="share_type" id="share_type" onchange="changeShareType();">
                                        <option value="0" selected="selected">Share (%)</option>
                                        <option value="1">Static Fee ($)</option>
                                        <option value="2">Mixed ($ + %)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-2" for="bprice">My Share (%)</label>
                                <div class="col-lg-3"><input type="text" id="share_cp" name="share_cp" class="form-control" value=""></div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-2" for="static">Static Fee ($)</label>
                                <div class="col-lg-3"><input type="text" id="share_fee" name="share_fee" class="form-control" disabled="disabled" value=""></div>
                            </div>
                        </div>
                    </div>


                    <div class="row"></div>

                    <input type="hidden" name="act" value="saveDealCP">
                    <input type="hidden" name="deal_id" value="10697">
                </form>
            </div>

            <script>
                $(document).ready(function(){
                    $('#cpname').tokenInput('tokens.php?pid=companies', {tokenLimit:1, theme: 'facebook',tokenFormatter:function(item){ return '<li><input type="hidden" id="existing" name="existing" value="'+item.id+'" /><p>' + item.name + '</p></li>' }});
                });
            </script>
        </div>
    </div>
    <button type="button" class="btn btn-success save-deal pull-right">SAVE CHANGES</button>


    <script>
        $('#pagetitle').html('VaterMark / Rights');

        var deal_id = "10697";
        var film_id = "2505";
        var tabToOpenAfterReload = "basic";



        $(function(){


            $("#DealContent li a").click(
                    function(e){
                        tabToOpenAfterReload = $(this).attr("class");
                    }
            );

            $("#content-providers-tokens").tokenInput("engine.php?act=tokens-get-cps", {
                theme: "facebook",
                tokenLimit:1,
                onAdd: function(item){
                    if(item.id == "-1"){
                        $("#cpAddToken").hide();
                        $("#cpAddNew").show();
                        $(this).tokenInput("clear");
                    }
                    $(".token-input-list-facebook").animate({borderColor:"green"},50,function(){$(this).css("borderColor","#8496ba")});
                }
            });

            $(".cancelCPAdd").click(function(){
                $("#cpname").val();
                $("#cpAddToken").show();
                $("#cpAddNew").hide();
            });

            $(".cpNameAddSave").click(function(){

                $("#content-providers-tokens").val();
                var cpname = $("#cpname").val();
                $.ajax({
                    type: "POST",
                    url: "engine.php",
                    data: "act=AddNewCPNamePL&cpname="+cpname,
                    dataType: "json",
                    success: function (data) {
                        $("#content-providers-tokens").tokenInput("add", {id: data, name: cpname});
                        $("#cpname").val();
                        $("#cpAddToken").show();
                        $("#cpAddNew").hide();
                    }
                });

            });
            $(".cpAttach").click(function(){
                var cpid = $("#content-providers-tokens").val();
                var cpname = $(".token-input-token-facebook p").text();
                $.ajax({
                    type: "POST",
                    url: "engine.php",
                    data: "act=attachCPToTitle&cpid="+cpid+"&film_id="+film_id,
                    dataType: "json",
                    success: function (data) {
                        $("#content-providers-tokens").tokenInput("clear");
                        $("#film_CPs").append("<option value=\""+cpid+"\">"+cpname+"</option>");
                        $("#AttachContentProvider").modal("hide");
                    }
                });
            });
        });

        $("#geoTemplates").on("change", function() {

            var targetList = $(".target > option").map(function() {
                var arr = [];
                arr.push({value:$(this).val(), content:$(this).text()});
                return arr;
            }).get();
            loadNewGeotemplate($(this).val(),targetList);
        });

        function changeShareType(){
            var type = $( "#share_type option:selected" ).val();
            if (type == 0){
                $("#share_fee").attr({ "disabled": "disabled" });
                $("#share_cp").removeAttr("disabled");
            }else if (type == 1){
                $("#share_fee").removeAttr("disabled");
                $("#share_cp").attr({ "disabled": "disabled" });
            }else if(type == 2){
                $("#share_fee").removeAttr("disabled");
                $("#share_cp").removeAttr("disabled");
            }
            return false;
        }
        function loadNewGeotemplate(geoId,targetList)
        {
            $.ajax({
                type: "POST",
                url: "engine.php",
                data: $("#addCountries").serialize()+"&targetList="+encodeURIComponent(JSON.stringify(targetList))+"&geoId="+geoId,
                dataType: "json",
                success: function (data) {
                    $("#TransferContainer").html("");
                    $(function() {
                        var t = $("#TransferContainer").bootstrapTransfer(
                                {"target_id": "multi-select-input",
                                    "height": "15em",
                                    "hilite_selection": true});

                        t.populate(
                                data.remaining,
                                data.target
                        );
                    });
                }
            });
        }
        function saveDealsCountries(){

            $(".filter-input").val("");
            $(".filter-input-target").val("");
            $(".glyphicon-search").trigger("click");


            var targetList = $(".target > option").map(function() {
                var arr = [];
                arr.push({value:$(this).val(), content:$(this).text()});
                return arr;
            }).get();
            return  $.ajax({type: "POST",url: "engine.php",
                data: "targetList="+encodeURIComponent(JSON.stringify(targetList))+"&film_id="+film_id+"&deal_id="+deal_id+"&act=save-deals-countries&film_id="+film_id,
                success: function (data) {
                    loadNewGeotemplate(1,targetList);
                }
            });
        }
        function saveCountriesPrices() {return $.ajax({type: "POST",url: "engine.php",data:  $("#contriesPrices").serialize()});}
        function saveCountryItem(id) {$.ajax({type: "POST",url: "engine.php",data:  $("#contriesPrices").serialize()+"&item_id="+id});}
        function saveDealInfo() {
            return $.ajax({type: "POST",url: "engine.php",data:  $("#dealInfo").serialize()});
        }
        function saveDealCP() { console.log("saveDealCP"); return $.ajax({type: "POST",url: "engine.php",data:  $("#dealCP").serialize()});}

        $(".save-deal").click(function(){

            $(".save-deal").html("<span class=\"save-dealp\">SAVING...</span>");
            console.log("saving");
            $.when(
                    saveDealInfo(),
                    saveDealCP(),
                    saveDealsCountries(),
                    saveCountriesPrices()
            ).done(function(){
                        $(".save-dealp").text("SAVE CHANGES");
                        reloadAll(tabToOpenAfterReload);
                    }).fail(function(){
                        $(".save-dealp").text("SAVE CHANGES");
                    }).always(function(){
                        $(".save-dealp").text("SAVE CHANGES");
                    });

            window.change1Count = 0;
            window.change2Count = 0;
            window.change3Count = 0;



        });



        function reloadAll(clicked){

            return $.ajax({
                type: "POST",
                url: "engine.php",
                data:  "act=reloadDeal&deal_id="+deal_id+"&film_id="+film_id,
                dataType: "json",
                success: function (data) {
                    $("#DealContent").html(data);

                    $( ".dealTabs li").removeClass("active");
                    $( ".tab-content div").removeClass("active");
                    $("."+clicked).parent().addClass("active");
                    $("#tab-"+clicked).addClass("active");
                    ChangeRentBuy(PricesTabsToOpen);
                }
            });
        }
        function clickTab(clicked)
        {
            var selfClass = "";
            $( ".dealTabs li a").each(function( index ) {
                selfClass = $(this).attr('class');
                if($(this).parent().attr('class') == "active")
                {
                    if (selfClass == "basic"){
                        if(window.change1Count == 1){
                            if (confirm("Would you like to save your changes before leaving this tab?")) {
                                saveDealInfo();
                                window.change1Count = 0;
                            }
                            reloadAll(clicked);

                        }
                    }
                    else if (selfClass == "addCountries"){
                        if(window.change2Count == 1){
                            if (confirm("Would you like to save your changes before leaving this tab?")) {
                                saveDealsCountries();
                                window.change2Count = 0;
                            }
                            reloadAll(clicked);
                        }
                    }
                    else if (selfClass == "countriesPrices"){
                        if(window.change3Count == 1){
                            if (confirm("Would you like to save your changes before leaving this tab?")) {
                                saveCountriesPrices();
                                window.change3Count = 0;
                            }
                            reloadAll(clicked);
                        }
                    }


                }
            });


        }

        $( document ).ready(function() {


            window.change1Count = 0;
            window.change2Count = 0;
            window.change3Count = 0;

            $(".changebut").click(function(){
                window.change2Count = 1;
            });
            $( ".changeselect" ).change(function() {
                window.change1Count = 1;
            });
            $( "#dealInfo input[type='text']" ).change(function() {
                window.change1Count = 1;
            });
            $( "#contriesPrices input[type='text']" ).change(function() {
                window.change3Count = 1;
            });

            //$.datepicker.setDefaults({dateFormat:"yy-mm-dd",changeYear:true});
            $(".dt").datepicker();

        });
    </script>

</div>
</div>
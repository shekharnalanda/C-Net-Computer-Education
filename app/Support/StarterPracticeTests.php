<?php

namespace App\Support;

class StarterPracticeTests
{
    public static function all(): array
    {
        return [
            self::test('DCA','DCA Computer Fundamentals – Set 1',[
                ['CPU का पूरा नाम क्या है?','Central Processing Unit','Computer Processing User','Central Print Unit','Control Program Unit','A'],
                ['MS Word मुख्य रूप से किस काम के लिए है?','Video editing','Document creation','Accounting','Programming','B'],
                ['Copy का keyboard shortcut क्या है?','Ctrl+X','Ctrl+P','Ctrl+C','Ctrl+S','C'],
                ['इनमें से कौन input device है?','Monitor','Printer','Speaker','Keyboard','D'],
                ['Internet पर website खोलने के लिए क्या उपयोग होता है?','Web browser','Calculator','Paint','Notepad','A'],
            ]),
            self::test('ADCA','ADCA Advanced Applications – Set 1',[
                ['Mail Merge का उपयोग किस लिए होता है?','एक ही letter कई recipients को भेजने','Photo editing','Database delete','Video play','A'],
                ['Tally में company data किससे संबंधित है?','Gaming','Accounting','Drawing','Coding','B'],
                ['HTML का उपयोग किसके लिए होता है?','Web page structure','Antivirus scan','Payroll only','Image printing','A'],
                ['Primary key की विशेषता क्या है?','Duplicate होती है','रिक्त ही रहती है','Record को uniquely identify करती है','केवल text रखती है','C'],
                ['Presentation slide show शुरू करने की key क्या है?','F2','F5','F8','F12','B'],
            ]),
            self::test('CCC','CCC Digital Literacy – Set 1',[
                ['OTP किसके साथ share करना सुरक्षित है?','Bank caller','Friend','किसी के साथ नहीं','Shopkeeper','C'],
                ['Email address में सामान्यतः कौन-सा चिन्ह होता है?','#','@','%','&','B'],
                ['UPI का उपयोग किस लिए होता है?','Digital payment','Photo editing','Typing','Printing','A'],
                ['Strong password में क्या होना चाहिए?','केवल नाम','केवल 12345','अक्षर, अंक और symbols','जन्मतिथि','C'],
                ['Cloud storage का उदाहरण कौन है?','Google Drive','Calculator','Notepad','Paint','A'],
            ]),
            self::test('TALLY','Tally Prime & GST – Set 1',[
                ['Accounting equation क्या है?','Assets = Liabilities + Capital','Sales = Purchase + Stock','Cash = Profit + Loss','GST = Income + Expense','A'],
                ['GST का पूरा नाम क्या है?','General Sales Tax','Goods and Services Tax','Government Service Tariff','Gross Service Total','B'],
                ['Purchase transaction दर्ज करने के लिए कौन-सा voucher है?','Receipt','Contra','Purchase','Payment','C'],
                ['Customer account सामान्यतः किस group में आता है?','Sundry Debtors','Fixed Assets','Capital Account','Indirect Expenses','A'],
                ['Trial Balance का उद्देश्य क्या है?','Logo बनाना','Debit और Credit totals जाँचना','Email भेजना','Stock print करना','B'],
            ]),
            self::test('EXCEL','Advanced Excel & MIS – Set 1',[
                ['Formula किस चिन्ह से शुरू होता है?','@','=','&','#','B'],
                ['SUM function क्या करता है?','Text delete','Values जोड़ता है','Sheet lock','Chart हटाता है','B'],
                ['Absolute cell reference का उदाहरण क्या है?','A1','$A$1','A:A','1A','B'],
                ['PivotTable किस लिए उपयोगी है?','Data summary और analysis','Typing speed','Photo crop','Email login','A'],
                ['VLOOKUP में lookup value कहाँ खोजी जाती है?','Table की पहली column','अंतिम row','Chart title','Footer','A'],
            ]),
            self::test('DTP','DTP & Graphic Design – Set 1',[
                ['CMYK color mode मुख्यतः कहाँ उपयोग होता है?','Printing','Audio','Database','Coding','A'],
                ['Photoshop में layers का लाभ क्या है?','Elements को अलग-अलग edit करना','Internet तेज करना','File rename','Sound record','A'],
                ['Vector graphics की विशेषता क्या है?','Resize पर quality बनी रहती है','केवल black होती है','Sound रखती है','Formula चलाती है','A'],
                ['Brochure में bleed क्यों दिया जाता है?','Cutting के बाद white edge से बचने','Password लगाने','Email भेजने','Font delete करने','A'],
                ['CorelDRAW किस प्रकार का software है?','Vector design','Accounting','Antivirus','Spreadsheet','A'],
            ]),
            self::test('WEB','Web Design & Development – Set 1',[
                ['HTML का पूरा नाम क्या है?','HyperText Markup Language','HighText Machine Language','Home Tool Markup Language','Hyper Transfer Main Link','A'],
                ['CSS का उपयोग किस लिए होता है?','Page styling','Database backup','Email hosting','Virus scan','A'],
                ['JavaScript क्या जोड़ता है?','Interactivity','Printer ink','Domain expiry','File compression only','A'],
                ['Responsive design का उद्देश्य क्या है?','अलग screen sizes पर सही layout','केवल desktop support','Internet बंद करना','Password हटाना','A'],
                ['Secure website URL सामान्यतः किससे शुरू होता है?','ftp://','file://','https://','mail://','C'],
            ]),
            self::test('PYTHON','Python Programming – Set 1',[
                ['Python में output दिखाने के लिए कौन-सा function है?','echo()','print()','show()','writehtml()','B'],
                ['List किस brackets में लिखी जाती है?','[]','{}','()','<>','A'],
                ['Condition के लिए कौन-सा keyword है?','loop','when','if','check','C'],
                ['len() function क्या देता है?','Length','Color','File name','Password','A'],
                ['Python में comment किससे शुरू होता है?','//','#','<!--','**','B'],
            ]),
            self::test('DIGITAL','Digital Marketing – Set 1',[
                ['SEO का मुख्य उद्देश्य क्या है?','Search visibility बढ़ाना','Computer format करना','Logo print करना','Payroll बनाना','A'],
                ['CTR का अर्थ क्या है?','Click Through Rate','Content Total Reach','Customer Time Record','Campaign Tax Report','A'],
                ['Organic traffic कहाँ से आता है?','Unpaid search results','केवल paid ads','Offline printer','USB drive','A'],
                ['Social media content calendar किस लिए है?','Posts plan और schedule करने','Password store करने','GST return','Code compile','A'],
                ['Google Analytics क्या मापता है?','Website traffic और behavior','Typing speed','Printer quality','Electricity','A'],
            ]),
            self::test('HARDWARE','Hardware & Networking – Set 1',[
                ['RAM कैसी memory है?','Volatile','Permanent optical','Paper','Mechanical only','A'],
                ['LAN का पूरा नाम क्या है?','Local Area Network','Large Access Node','Linked Account Number','Long Area Name','A'],
                ['Router का मुख्य काम क्या है?','Networks के बीच data route करना','Document type करना','Image crop करना','Audio record करना','A'],
                ['SSD की तुलना में HDD में सामान्यतः क्या होता है?','Moving parts','No storage','Only RAM','No file system','A'],
                ['IP address किसकी पहचान करता है?','Network device','Printed page','Keyboard key','Folder color','A'],
            ]),
            self::test('AI','AI Tools for Study & Work – Set 1',[
                ['AI prompt क्या है?','AI को दिया गया निर्देश या प्रश्न','Computer cable','Printer paper','Bank voucher','A'],
                ['AI output उपयोग करने से पहले क्या करना चाहिए?','Accuracy verify करनी चाहिए','बिना पढ़े publish','Password देना','OTP share करना','A'],
                ['Sensitive data AI tool में डालना कैसा है?','Avoid करना चाहिए','हमेशा जरूरी','Public करना चाहिए','कोई फर्क नहीं','A'],
                ['Generative AI क्या बना सकता है?','Text और images','केवल keyboard','केवल बिजली','Physical RAM','A'],
                ['Responsible AI use में क्या शामिल है?','Privacy, verification और attribution','Fake information फैलाना','Copyright ignore करना','Passwords share करना','A'],
            ]),
            self::test('DATA','Data Entry & Office Assistant – Set 1',[
                ['Numeric keypad किस काम में मदद करता है?','तेज number entry','Photo editing','Network routing','Video rendering','A'],
                ['Ctrl+S का उपयोग क्या है?','Save','Search only','Shutdown','Select printer','A'],
                ['Data validation क्यों उपयोग होती है?','गलत entry सीमित करने','Font बड़ा करने','Internet connect','Audio play','A'],
                ['Official email में subject कैसा होना चाहिए?','स्पष्ट और संक्षिप्त','खाली','केवल emoji','बहुत अस्पष्ट','A'],
                ['Spreadsheet में row और column के intersection को क्या कहते हैं?','Cell','Slide','Layer','Voucher','A'],
            ]),
        ];
    }

    private static function test(string $course,string $title,array $questions): array
    {
        return [
            'starter_key'=>strtolower($course).'-set-1','course_code'=>$course,'title'=>$title,
            'duration_minutes'=>15,'pass_percentage'=>40,
            'questions'=>array_map(fn(array $q,int $i):array=>[
                'id'=>'starter-'.strtolower($course).'-q'.($i+1),'prompt'=>$q[0],
                'options'=>['A'=>$q[1],'B'=>$q[2],'C'=>$q[3],'D'=>$q[4]],'correct'=>$q[5],
            ],$questions,array_keys($questions)),
        ];
    }
}

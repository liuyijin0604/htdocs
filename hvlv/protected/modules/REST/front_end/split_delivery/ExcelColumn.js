export default class ExcelColumn{
    static Field_Weight = 'weight';
    static Field_Sub_Number = 'sub_number';
    
    static listColumn =[
        {title:'主单号（HBN)',field:'main_number'},
        {title:'分单号',field:'sub_number'},
        {title:'KG',field:ExcelColumn.Field_Weight},
        {title:'CBM',field:'actual_volume'},
        {title:'Piece',field:'quantity'},
        {title:'ADDRESS',field:'address'},
        {title:'Suburb',field:'suburb'},
        {title:'P/C',field:'postcode'},
        {title:'STATE',field:'state'},
        {title:'Contact',field:'contact'},
        {title:'TLE',field:'phone'},
    ];
    
    static funcGetField(strTitle){
        for(let i = 0 ; i < this.listColumn.length ; i++){
            if (this.listColumn[i].title === strTitle){
                return this.listColumn[i].field;
            }
        }
    }
    
    

   
}
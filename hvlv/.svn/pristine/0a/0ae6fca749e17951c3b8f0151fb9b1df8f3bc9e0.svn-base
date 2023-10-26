import React, { Component } from 'react'

import {
    Row, Card, DatePicker, Button, Table,message, Tag,} from 'antd';
import { FileExcelOutlined, PrinterOutlined
} from '@ant-design/icons';
import 'antd/dist/antd.css';
import './split_delivery.css';
import ModalImport from './ModalImport';
import ModalScanPrint from './ModalScanPrint';
import ExcelColumn from './ExcelColumn';
import axios from 'axios'
import moment from 'moment';
import Str from './Str';



export default class SplitDelivery extends Component {
    constructor(props) {
        super(props);

        this.state = {
            objTimeTange: [moment().startOf('day').subtract(7, 'days'), moment().endOf('day')],

            isTableLoading: false,
            objDataSource:[],

            isVisibleImportModal: false,

            fileList: [],
            uploading: false,

        };
        this.refModalImport = React.createRef();
        this.refModalScanPrint = React.createRef();
    }
    
    componentDidMount() {
        this.funcGetRecords();
    }

    funcGetRecords = async () => {
        if (this.state.isTableLoading) {
            return;
        }

        this.setState({ isTableLoading: true });

        let respond = await axios.request({
            url: '/rest/v1/splitDelivery/records',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            method: 'get',
            params: {
                startTime: this.state.objTimeTange[0].format('YYYY-MM-DD HH:mm:ss'),
                endTime: this.state.objTimeTange[1].format('YYYY-MM-DD HH:mm:ss'),
            }
        });

        if (respond.data.isSuccess) {
            let objData = respond.data.data;
            this.setState({ objDataSource: objData, });
            message.success('success !');
        }
        else {
            message.error('error !');
        }

        this.setState({ isTableLoading: false });
    }



    render() {
        const columns = ExcelColumn.listColumn.map(item => { 
            let obj = { title: item.title, dataIndex: item.field, key: item.field,} ;
            if(obj.key === ExcelColumn.Field_Sub_Number){
                obj.render = (text,record)=>{
                    if(record[Str.print_time] !== null){
                        return <span>{text}<Tag icon={<PrinterOutlined />} color="blue">Printed</Tag></span>;
                    }
                    return text;
                }
            }
            return obj
        });
        
        const { RangePicker } = DatePicker;
        
        
        return (
            <div className="outside_margin">
                <ModalImport ref={this.refModalImport}></ModalImport>
                <ModalScanPrint ref={this.refModalScanPrint}></ModalScanPrint>

                <Row>
                    <h3 className="card_tilte">Split Delivery</h3>
                    <Button onClick={() => this.refModalImport.current.funcShow()} icon={<FileExcelOutlined />} className="margin_l_a" type="link" >Import</Button>
                    <Button onClick={() => this.refModalScanPrint.current.funcShow()} icon={<PrinterOutlined />} type="link" >Print</Button>
                </Row>

                <Card
                    type="inner" 
                    // title="Split Delivery"
                    title = {<RangePicker value={this.state.objTimeTange} onChange={(dates, dateStrings) => { this.setState({ objTimeTange: dates, }); }} />}
                        
                    extra={<Button type="primary" className="search_button" onClick={this.funcGetRecords}>Search</Button>}
                >
                    <Table
                        columns={columns}
                        dataSource={this.state.objDataSource}
                        loading={this.state.isTableLoading}
                    />

                </Card>
            </div>
        )
    }
}

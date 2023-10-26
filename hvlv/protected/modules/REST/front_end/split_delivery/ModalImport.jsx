import React, { Component } from 'react'

import {
    Row, Button, Table, Modal, Upload,
    Spin, message, Input, Select,Tag,
} from 'antd';
import { FileExcelOutlined, CloseCircleOutlined} from '@ant-design/icons';
import 'antd/dist/antd.css';
import XLSX from 'xlsx';
import './split_delivery.css';
import ExcelColumn from './ExcelColumn.js'
import axios from 'axios'
import Str from './Str'
import DepotId from '../depot_id/DepotId'
import CourierID from '../courier_id/CourierId'

export default class ModalImport extends Component {
    constructor(props) {
        super(props);

        this.state = {
            isSpin: false,
            isVisible: false,


            fileList: [],

            objDataSource: [],


            listOrg: [],
            strOrgId: null,
            strDepotId: null,
            strCourierId: null,

        };

        this.refModalEdit = React.createRef();
    }

    componentDidMount() {
        this.funcGetOrgList();
    }

    funcShow = () => {
        this.setState({ isVisible: true, })
    }

    funcGetOrgList = async () => {
        let respond = await axios.request({
            url: '/rest/v1/SplitDelivery/GetOrgList',
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        })

        if (respond.data.isSuccess) {
            this.setState({
                listOrg: respond.data.data,
            })
            // message.success('success !');
        }
        else {
            message.error('error : GetOrgList!');
        }
    }

    funcSubmit = async () => {
        if (this.state.isSpin) {
            return;
        }
        
        if(this.state.strOrgId === null||this.state.strDepotId === null||this.state.strCourierId === null){
            message.error('please select ...');
            return;
        }

        this.setState({ isSpin: true });


        for (let i = 0; i < this.state.objDataSource.length; i++) {
            this.state.objDataSource[i][Str.org_id] = this.state.strOrgId;
            this.state.objDataSource[i][Str.depot_id] = this.state.strDepotId;
            this.state.objDataSource[i][Str.courier_id] = this.state.strCourierId;
        }

        const form = new FormData();
        form.append('data', JSON.stringify(this.state.objDataSource));

        let respond = await axios.request({
            url: '/rest/v1/SplitDelivery/import',
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            data: form,
        })

        if (respond.data.isSuccess) {
            this.setState({
                isSpin: false,
                isVisible: false,
            })
            message.success('success !');
        }
        else {
            message.error('error !');
        }

    }


    funcExcel2Json = async (objFile) => {
        let objResult = await function (objFile) {
            return new Promise((resolve, reject) => {
                let objFileReader = new FileReader();
                objFileReader.onload = function (e) {
                    resolve(e.target.result);
                };
                objFileReader.onerror = reject;
                objFileReader.readAsBinaryString(objFile);
            });
        }(objFile);

        let excelData = XLSX.read(objResult, { type: 'binary' });
        return XLSX.utils.sheet_to_json(excelData.Sheets[excelData.SheetNames[0]]); //excelData.SheetNames[0]是获取Sheets中第一个Sheet的名字//excelData.Sheets[Sheet名]获取第一个Sheet的数据
    }

    funcLoadTable = async (objFile) => {
        let objExcelJson = await this.funcExcel2Json(objFile);
        let objData = objExcelJson.map(item => {
            let obj = {};
            obj['key'] = item.__rowNum__;
            ExcelColumn.listColumn.forEach(element => {
                obj[element.field] = item[element.title];
            });
            return obj;
        });
        this.setState({ objDataSource: objData });
    }



    render() {
        const columns = ExcelColumn.listColumn.map(item => {
            let obj = { title: item.title, dataIndex: item.field, key: item.field, } ;
            if(obj.key === ExcelColumn.Field_Weight){
                obj.render = text=>{
                    // var reg = new RegExp('[1-9]([0-9]*|.)[0-9]*') 
                    // if(reg.test(text)){
                    //     return text;
                    // }
                    let numWeight = parseFloat(text);
                    if(numWeight !== null && numWeight>0){
                        return text;
                    }
                    return <a>{text}<Tag icon={<CloseCircleOutlined />} color="error">Error!</Tag></a>;
                }
            }
            return obj;
        });
        
        
        const { Option } = Select;
        let objListOrgOption = this.state.listOrg.map(item => <Option key={item.id}>{item.name}</Option>)
        const objDepotIdOption = DepotId.listId2Name.map(item => <Option key={item.id}>{item.name}</Option>)
        const objCourierIdOption = CourierID.listId2Name.map(item => <Option key={item.id}>{item.name}</Option>)

        
        return (
            <Modal
                width="1200px"
                title="Import Excel"
                visible={this.state.isVisible}
                onCancel={() => !this.state.isSpin ? this.setState({ isVisible: false, }) : null}
                footer={[<Row>
                    <Upload
                        showUploadList={false}
                        onRemove={file => {
                            this.setState(state => {
                                const index = state.fileList.indexOf(file);
                                const newFileList = state.fileList.slice();
                                newFileList.splice(index, 1);
                                return {
                                    fileList: newFileList,
                                };
                            });
                        }}
                        beforeUpload={async file => {
                            await this.funcLoadTable(file);
                            return false;
                        }}
                        fileList={this.state.fileList}
                    >
                        <Button type="link" icon={<FileExcelOutlined />}>Select File</Button>
                    </Upload>

                    <Select
                        showSearch
                        style={{ width: 330,textAlign:"left" }}
                        placeholder="Select a Org"
                        
                        value = {this.state.strOrgId}
                        onChange={value=>this.setState({strOrgId:value})}
                        
                        // onFocus={onFocus}
                        // onBlur={onBlur}
                        // onSearch={onSearch}
                        // optionFilterProp="children"
                        filterOption={(input, option) =>
                            option.children.toLowerCase().indexOf(input.toLowerCase()) >= 0
                        }
                    >
                        {objListOrgOption}
                    </Select>
                    
                    <Select
                        style={{ width: 120,textAlign:"left" }}
                        placeholder="Select a Depot"
                        value = {this.state.strDepotId}
                        onChange={value=>this.setState({strDepotId:value})}
                    >
                        {objDepotIdOption}
                    </Select>
                    <Select
                        style={{ width: 120,textAlign:"left" }}
                        placeholder="Select a Courier"
                        value = {this.state.strCourierId}
                        onChange={value=>this.setState({strCourierId:value})}
                    >
                        {objCourierIdOption}
                    </Select>

                    <Button onClick={this.funcSubmit} type="primary" className="margin_l_a">
                         Submit
                    </Button>

                </Row>]}
            >
                <Spin tip="Loading..." spinning={this.state.isSpin}>
                    <Table
                        columns={columns} dataSource={this.state.objDataSource}
                        size="small">
                    </Table>
                </Spin>
            </Modal>
        )
    }
}

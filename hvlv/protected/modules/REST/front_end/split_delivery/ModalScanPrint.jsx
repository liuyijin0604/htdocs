import React, { Component } from 'react'
import { Modal, Button, Input, Row } from 'antd';
import 'antd/dist/antd.css';
import './split_delivery.css';
import axios from 'axios'

export default class ModalScanPrint extends Component {
    constructor(props) {
        super(props);

        this.state = {
            strSubNumber: '',
            isModalVisible: false,

        };
    }

    funcShow = () => {
        this.setState({ isModalVisible: true, });
    }

    funcClose = () => {
        this.setState({ isModalVisible: false, });
    }

    funcPrint = () => {
        window.open("/rest/v1/SplitDelivery/scanprint?sub_number="+this.state.strSubNumber, "_blank");
    }

    render() {
        return (
            <Modal
                title="Scan Print"
                visible={this.state.isModalVisible}
                // onOk={handleOk}
                // onCancel={handleCancel}
                // footer={<Button onClick={this.funcClose} type="danger" className="margin_l_a">
                footer={<Button onClick={this.funcClose} type="danger">
                    Close
                    </Button>
                }
                closable ={false}
            >
                <Row>
                    <Input 
                        value = {this.state.strSubNumber}
                        onChange = {e=>this.setState({strSubNumber:e.target.value})}
                        addonAfter={<Button type="primary" onClick={this.funcPrint}>Print Label</Button>}
                    >
                            
                    </Input>
                    
                </Row>

            </Modal>
        )
    }
}

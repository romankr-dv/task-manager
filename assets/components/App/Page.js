import React from 'react';
import Header from "./Header/Header";
import Sidebar from "./Sidebar/Sidebar";
import {Tooltip} from "react-tooltip";
import 'react-tooltip/dist/react-tooltip.css'

const Page = ({children, sidebar}) => {
  return (
    <div>
      <Header/>
      <div className="container-fluid main-container">
        <div className="row row-offcanvas row-offcanvas-left">
          <Sidebar root={sidebar.root} onSearch={sidebar.onSearch} reminderNumber={sidebar.reminderNumber}/>
          <div className="col-xs-12 col-sm-9 content">
            <div className="panel panel-default">
              {children}
            </div>
          </div>
        </div>
      </div>
      <Tooltip id="basic-tooltip" />
    </div>
  );
}

export default Page;

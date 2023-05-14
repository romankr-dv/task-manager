import React from 'react';
import PanelHeading from "../../App/PanelHeading/PanelHeading";
import Button from "../../App/Common/Button";
import PanelHeadingTask from "../../App/PanelHeading/PanelHeadingTask/PanelHeadingTask";
import OpenIcon from "../../App/Common/OpenIcon";

const HistoryPanelHeading = ({title, icon, task, events}) => {
  return (
    <PanelHeading title={title} icon={icon}>
      {task ? <PanelHeadingTask task={task} backLink={'/history'}/> : null}
      <Button onClick={events.reload}><OpenIcon name="reload"/></Button>
    </PanelHeading>
  );
}

export default HistoryPanelHeading;
